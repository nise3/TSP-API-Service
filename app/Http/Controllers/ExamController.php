<?php

namespace App\Http\Controllers;


use App\Models\Batch;
use App\Models\BatchExam;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Result;
use App\Services\BatchCertificateTemplateService;
use App\Services\ExamService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ExamController extends Controller
{
    /**
     * @var ExamService
     */
    public ExamService $examService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * @param ExamService $ExamService
     */

    public function __construct(ExamService $ExamService)
    {
        $this->examService = $ExamService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Exam::class);
        $filter = $this->examService->filterValidator($request)->validate();
        $response = $this->examService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $this->authorize('view', Exam::class);
        $exam = $this->examService->getOneExamType($id);
        $response = [
            "data" => $exam,
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
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Exam::class);
        $validatedData = $this->examService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $examType = $this->examService->storeExamType($validatedData);
            $validatedData['exam_type_id'] = $examType->id;
            $examIds = $this->examService->storeExam($validatedData);
            $validatedData['exam_ids'] = $examIds;
            if (!empty($validatedData['sets']) || !empty($validatedData['offline']['sets'])) {
                $examSets = $this->examService->storeExamSets($validatedData);
                $validatedData['sets'] = $examSets;
            }
            if (!empty($validatedData['type']) && !in_array($validatedData['type'], Exam::EXAM_TYPES_WITHOUT_QUESTION)) {
                $this->examService->storeExamSections($validatedData);
            }
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Exam added successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->authorize('update', Exam::class);

        $examType = ExamType::findOrFail($id);
        $validatedData = $this->examService->validator($request, $id)->validate();

        DB::beginTransaction();
        try {
            $this->examService->updateExamType($examType, $validatedData);
            $examIds = $this->examService->updateExam($validatedData);
            $validatedData['exam_ids'] = $examIds;
            if (!empty($validatedData['type']) && !in_array($validatedData['type'], Exam::EXAM_TYPES_WITHOUT_QUESTION)) {
                $this->examService->deleteExamQuestionRelatedDataForUpdate($examIds);
            }
            if (!empty($validatedData['sets']) || !empty($validatedData['offline']['sets'])) {
                $examSets = $this->examService->storeExamSets($validatedData);
                $validatedData['sets'] = $examSets;
            }
            if (!empty($validatedData['type']) && !in_array($validatedData['type'], Exam::EXAM_TYPES_WITHOUT_QUESTION)) {
                $this->examService->storeExamSections($validatedData);
            }
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Exam updated successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', Exam::class);

        $examType = ExamType::findOrFail($id);
        DB::beginTransaction();
        try {
            $this->examService->destroy($examType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Exam  deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function getExamYouthList(Request $request, int $id): JsonResponse
    {
        $this->authorize('viewAnyYouthExam', Exam::class);

        $filter = $this->examService->examYouthListFilterValidator($request)->validate();
        $response = $this->examService->getExamYouthList($filter, $id);
        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function getExamQuestionPaper(int $id): JsonResponse
    {
        $examData = $this->examService->getExamQuestionPaper($id);

        $exam = Exam::findOrFail($id);
        $examStartTime = CarbonImmutable::create($exam->start_date);
        if (in_array($exam->type, Exam::EXAM_TYPES_WITHOUT_QUESTION)) {
            $examEndTime = $exam->end_date;
        } else {
            $examEndTime = $examStartTime->addMinutes($exam->duration);
        }
        throw_if($this->startTime->lt($examStartTime), ValidationException::withMessages(["Exam has not started"]));
        throw_if($this->startTime->gt($examEndTime), ValidationException::withMessages(["Exam is over"]));

        $response = [
            "data" => $examData ?? null,
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
     * @throws ValidationException|Throwable
     */
    public function submitExamPaper(Request $request): JsonResponse
    {
        $validatedData = $this->examService->examPaperSubmitValidator($request)->validate();

        $exam = Exam::findOrFail($validatedData['exam_id']);
        $examStartTime = CarbonImmutable::create($exam->start_date);
        if (in_array($exam->type, Exam::EXAM_TYPES_WITHOUT_QUESTION)) {
            $examEndTime = $exam->end_date;
        } else {
            $examEndTime = $examStartTime->addMinutes($exam->duration);
        }
        throw_if($this->startTime->lt($examStartTime), ValidationException::withMessages(["Exam has not started"]));
        throw_if($this->startTime->gt($examEndTime), ValidationException::withMessages(["Exam is over"]));

        DB::beginTransaction();
        try {
            $this->examService->submitExamQuestionPaper($validatedData, $this->startTime);
            $response = [
                "_response_status" => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Exam paper submitted successfully.",
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
     * @param int $examId
     * @param int $youthId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function previewYouthExam(int $examId, int $youthId): JsonResponse
    {
        $this->authorize('viewYouthExam', Exam::class);

        $youthExamPreview = $this->examService->getPreviewYouthExam($examId, $youthId);
        $response = [
            "data" => $youthExamPreview ?? null,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $examId
     * @param int $youthId
     * @return JsonResponse
     */
    public function previewPublicYouthExam(int $examId, int $youthId): JsonResponse
    {

        $youthExamPreview = $this->examService->getPreviewYouthExam($examId, $youthId);
        $response = [
            "data" => $youthExamPreview ?? null,
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
     * @throws ValidationException|AuthorizationException
     */
    public function youthExamMarkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateYouthExam', Exam::class);
        $validatedData = $this->examService->youthExamMarkUpdateValidator($request)->validate();
        $this->examService->youthExamMarkUpdate($validatedData, $this->startTime);
        $response = [
            "data" => $youthExamMarkUpdateData ?? null,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "youth exam mark updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException|ValidationException
     */
    public function youthBatchExamsMarkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateYouthExam', Exam::class);
        $validatedData = $this->examService->youthBatchExamMarkUpdateValidator($request)->validate();
        $this->examService->youthBatchExamMarkUpdate($validatedData);
        $response = [
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "youth Batch exam mark updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     * @throws Throwable
     */
    public function examPublish(Request $request, int $id): JsonResponse
    {
        $this->authorize('create', Exam::class);
        $examType = ExamType::findOrFail($id);

        $exams = Exam::where('exam_type_id', $id)->get()->toArray();
        foreach ($exams as $exam) {
            $examStartDate = Carbon::create($exam['start_date']);
            throw_if($this->startTime->gte($examStartDate), ValidationException::withMessages(["Exam can not be published or unpublished"]));
        }

        $validatedData = $this->examService->examPublishValidator($request)->validate();
        $this->examService->examPublish($validatedData, $examType, $this->startTime);

        if ($validatedData['is_published'] == Exam::EXAM_PUBLISHED) {
            $message = "exam published successfully";
        } else {
            $message = "exam unpublished successfully";
        }
        $response = [
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    public function youthAssessmentList(Request $request, int $fourIrInitiativeId): JsonResponse
    {
        $filter = $this->examService->youthAssessmentValidator($request)->validate();
        $response = $this->examService->getYouthAssessmentList($filter, $fourIrInitiativeId);
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     * @throws Throwable
     */
    public function publishExamResult(Request $request, int $id): JsonResponse
    {
        $this->authorize('create', Exam::class);

        $validatedData = $this->examService->resultPublishValidator($request)->validate();
        $isAllExamYouthMarkUpdateDone = $this->examService->isExamAllYouthMarkUpdatedDone($validatedData['batch_id'],$id);

        $batch = Batch::findOrFail($validatedData['batch_id']);

        $batchExamData = BatchExam::where('exam_type_id', $id)->where('batch_id', $validatedData['batch_id'])->first();

        if($batchExamData->exam_result_published_at){
            return Response::json(formatErrorResponse(["error_code" => "exam_result_already_published"], $this->startTime, "Exam Result Already published!"));
        }

        if (!$isAllExamYouthMarkUpdateDone) {
            return Response::json(formatErrorResponse(["error_code" => "mark_update_not_complete"], $this->startTime, "All youth mark update not completed!"));
        }

        $this->examService->publishExamResult($validatedData, $id, $batch, $this->startTime);

        $message = $validatedData['is_published'] == Result::RESULT_PUBLISHED ? "Result published successfully" : "Result unpublished successfully";

        $response = formatSuccessResponse(null, $this->startTime, $message);


        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
