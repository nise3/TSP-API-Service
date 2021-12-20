<?php

namespace App\Http\Controllers;

use App\Events\BatchCalender\BatchCalenderYouthBatchAssignEvent;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\CourseEnrollment;
use App\Services\CommonServices\MailService;
use App\Services\CourseEnrollmentService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Events\CourseEnrollment\CourseEnrollmentEvent;

class CourseEnrollmentController extends Controller
{

    /**
     * @var CourseEnrollmentService
     */
    public CourseEnrollmentService $courseEnrollService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * CourseEnrollmentController constructor.
     * @param CourseEnrollmentService $courseEnrollService
     */
    public function __construct(CourseEnrollmentService $courseEnrollService)
    {
        $this->courseEnrollService = $courseEnrollService;
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
     * @throws Exception
     */
    public function sendVerificationCode(Request $request, int $id): JsonResponse
    {
        $courseEnrollment = CourseEnrollment::findOrFail($id);
        $validated = $this->courseEnrollService->smsCodeValidation($request)->validate();
        $sendSmsStatus = $this->courseEnrollService->sendSmsVerificationCode($courseEnrollment, $validated['verification_code']);

        $response = [
            '_response_status' => [
                "success" => $sendSmsStatus,
                "code" => $sendSmsStatus ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => $sendSmsStatus ? "Sms Code Successfully Send." : "Unprocessable Request",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    public function reSendVerificationCode(int $id): JsonResponse
    {
        $courseEnrollment = CourseEnrollment::findOrFail($id);
        $sendSmsStatus = $this->courseEnrollService->resendCode($courseEnrollment);
        $response = [
            '_response_status' => [
                "success" => $sendSmsStatus,
                "code" => $sendSmsStatus ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => $sendSmsStatus ? "Sms Code Successfully ReSend." : "Unprocessable Request",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
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
            $this->courseEnrollService->assignBatch($validated);
            $this->courseEnrollService->sendMailYouthAfterBatchAssign($validated);

            $batch = Batch::findOrFail($validated['batch_id']);
            $courseEnrollment = CourseEnrollment::findOrFail($validated['batch_id']);

            $calenderEventPayload = [
                'batch_title' => $batch->title,
                'batch_title_en' => $batch->title_en,
                'youth_id' => $courseEnrollment->youth_id,
                'enrollment_id' => $courseEnrollment->id,
                'batch_id' => $batch->id,
                'batch_start_date' => $batch->batch_start_date,
                'batch_end_date' => $batch->batch_end_date
            ];

            /** Trigger Event to Cms Service via RabbitMQ  */
            event(new BatchCalenderYouthBatchAssignEvent($calenderEventPayload));

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
