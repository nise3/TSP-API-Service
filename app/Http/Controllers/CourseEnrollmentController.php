<?php

namespace App\Http\Controllers;

use App\Events\BatchCalender\BatchCalenderYouthBatchAssignEvent;
use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\CourseEnrollment;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\CourseEnrollmentBulkEntryService;
use App\Services\CourseEnrollmentService;
use App\Traits\Scopes\SagaStatusGlobalScope;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Events\CourseEnrollment\CourseEnrollmentEvent;

class CourseEnrollmentController extends Controller
{

    /**
     * @var CourseEnrollmentService
     */
    public CourseEnrollmentService $courseEnrollService;
    public CourseEnrollmentBulkEntryService $courseEnrollmentBulkEntryService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * CourseEnrollmentController constructor.
     * @param CourseEnrollmentService $courseEnrollService
     */
    public function __construct(CourseEnrollmentService $courseEnrollService, CourseEnrollmentBulkEntryService $courseEnrollmentBulkEntryService)
    {
        $this->courseEnrollService = $courseEnrollService;
        $this->courseEnrollmentBulkEntryService = $courseEnrollmentBulkEntryService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->filterValidator($request)->validate();
        $response = $this->courseEnrollService->getCourseEnrollmentList($validated, $this->startTime);
        return Response::json($response);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getYouthEnrollCourses(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->youthEnrollCoursesFilterValidator($request)->validate();
        $response = $this->courseEnrollService->getYouthEnrollCourses($validated, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getInstituteTraineeYouths(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->instituteTraineeYouthsFilterValidator($request)->validate();
        $response = $this->courseEnrollService->getInstituteTraineeYouths($validated, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $data = $this->courseEnrollService->getOneCourseEnrollment($id);
        $response = [
            "data" => $data ?: [],
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function courseEnrollment(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->courseEnrollmentValidator($request)->validate();
        DB::beginTransaction();
        try {
            $validated["verification_code"] = generateOtp(4);
            $validated['verification_code_sent_at'] = Carbon::now();
            $courseEnroll = $this->courseEnrollService->enrollCourse($validated);
            $this->courseEnrollService->storeEnrollmentAddresses($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentEducations($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentProfessionalInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentGuardianInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentMiscellaneousInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentPhysicalDisabilities($validated, $courseEnroll);

            $this->courseEnrollService->sendSmsVerificationCode($courseEnroll, $validated["verification_code"]);

            unset($validated['email']); // youth can't update email. So remove this from array
            unset($validated['mobile']); // youth can't update mobile. So remove this from array

            /** Trigger EVENT to Youth Service via RabbitMQ  */
            $validated['enrollment_id'] = $courseEnroll->id;
            event(new CourseEnrollmentEvent($validated));

            $response = [
                "data" => $courseEnroll,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Course enroll successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception|ValidationException
     */
    public function courseEnrollmentExcelFormat(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollmentBulkEntryService->buildExcelValidation($request)->validate();
        $data = $this->courseEnrollmentBulkEntryService->buildImportExcel($validated['course_id'], $validated['batch_id']);
        $response = [
            "data"=>$data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Course enroll bulk import is successfully done",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function courseEnrollmentBulkImport(Request $request): JsonResponse
    {
        $excelFileFormatValidation = $this->courseEnrollmentBulkEntryService->excelFileFormatValidation($request)->validate();
        $file = $request->file('course_enrollment_excel_file');
        $excelData = Excel::toCollection(new \App\Models\CourseEnrollmentBulkImport(), $file)->toArray();
        unset($excelFileFormatValidation['course_enrollment_excel_file']);
        $payload = $this->courseEnrollmentBulkEntryService->purseExcelAndInsert($excelData[0], $excelFileFormatValidation);
        DB::beginTransaction();
        try {
            foreach ($payload as $validatedData) {
                $validatedData['skills'] = $this->courseEnrollmentBulkEntryService->getSkills($validatedData['course_id']);
                $validatedData = array_merge($validatedData, $validatedData['address_info']['present_address']);
                $responseMessage = ServiceToServiceCall::updateOrCreateYouthUser($validatedData);
                if ($responseMessage->status() == ResponseAlias::HTTP_OK && $responseMessage->json("data")) {
                    $responseMessage = $responseMessage->json("data");
                    $validatedData['youth_id'] = $responseMessage['id'];
                    $validatedData['youth_code'] = $responseMessage['code'];
                    $courseEnroll = $this->courseEnrollService->enrollCourse($validatedData);
                    $this->courseEnrollService->storeEnrollmentAddresses($validatedData, $courseEnroll);
                    $this->courseEnrollService->storeEnrollmentEducations($validatedData, $courseEnroll);
                    $this->courseEnrollService->storeEnrollmentProfessionalInfo($validatedData, $courseEnroll);
                    $this->courseEnrollService->storeEnrollmentGuardianInfo($validatedData, $courseEnroll);
                    $this->courseEnrollService->storeEnrollmentMiscellaneousInfo($validatedData, $courseEnroll);
                    $this->courseEnrollService->storeEnrollmentPhysicalDisabilities($validatedData, $courseEnroll);
                }

            }
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Course enroll bulk import is successfully done",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, $response["_response_status"]["code"]);
    }

    /**
     * @throws Exception
     */
    public function verifyCode(Request $request, int $id): JsonResponse
    {
        Log::info("veryCodePayload" . json_encode($request->all()));

        $validated = $this->courseEnrollService->smsCodeValidation($request)->validate();
        Log::info("validated data: " . json_encode($validated));

        [$verifyStatus, $verifyMessage] = $this->courseEnrollService->verifySMSCode($id, $validated['verification_code']);
        $statusCode = $verifyStatus ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
        $response = [
            "data" => [
                "free_course" => $this->courseEnrollService->isFreeCourse($id)
            ],
            '_response_status' => [
                "success" => $verifyStatus,
                "code" => $statusCode,
                "message" => $verifyMessage,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, $statusCode);
    }

    /**
     * @throws Exception
     */
    public function reSendVerificationCode(int $id): JsonResponse
    {
        $courseEnrollment = CourseEnrollment::findOrFail($id);
        $sendSmsStatus = $this->courseEnrollService->resendCode($courseEnrollment);
        $statusCode = $sendSmsStatus ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
        $response = [
            '_response_status' => [
                "success" => $sendSmsStatus,
                "code" => $statusCode,
                "message" => $sendSmsStatus ? "Sms Code Successfully ReSend." : "Unprocessable Request",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, $statusCode);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function assignBatch(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $this->courseEnrollService->batchAssignmentValidator($request)->validate();
            $courseEnrollmentDataBeforeUpdate = CourseEnrollment::findOrFail($validated['enrollment_id']);

            $batch = Batch::findOrFail($validated['batch_id']);
            $this->courseEnrollService->assignBatch($validated, $batch);

            /** Remove SagaStatusGlobalScope as in above assignBatch() method already saga_status has been changed to UPDATE_PENDING */
            $courseEnrollmentDataAfterUpdate = CourseEnrollment::withoutGlobalScope(SagaStatusGlobalScope::class)->findOrFail($validated['enrollment_id']);

            $calenderEventPayload = [
                'batch_title' => $batch->title,
                'batch_title_en' => $batch->title_en,
                'youth_id' => $courseEnrollmentDataAfterUpdate->youth_id,
                'enrollment_id' => $courseEnrollmentDataAfterUpdate->id,
                'batch_id' => $batch->id,
                'batch_start_date' => $batch->batch_start_date,
                'batch_end_date' => $batch->batch_end_date,
                'saga_previous_data' => $courseEnrollmentDataBeforeUpdate->toArray()
            ];

            /** Trigger Event to Cms Service via RabbitMQ  */
            event(new BatchCalenderYouthBatchAssignEvent($calenderEventPayload));

            /** Mail send */
            $to = array($courseEnrollmentDataAfterUpdate->email);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "Batch Assignment Information";
            $message = "Congratulation,You have assigned in " . $courseEnrollmentDataAfterUpdate->batch->title;
            $messageBody = MailService::templateView($message);
            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();

            /** Sms send */
            $recipient = $courseEnrollmentDataAfterUpdate->mobile;
            $smsMessage = "You have assigned in " . $courseEnrollmentDataAfterUpdate->batch->title;
            $smsService = new SmsService();
            $smsService->sendSms($recipient, $smsMessage);

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Batch assign successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function rejectCourseEnrollment(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->rejectCourseEnrollmentValidator($request)->validate();
        $this->courseEnrollService->rejectCourseEnrollmentApplication($validated);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Course Enrollment Rejected Successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
