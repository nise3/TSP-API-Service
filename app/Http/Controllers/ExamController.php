<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use App\Models\ExamType;
use App\Services\BatchService;
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

        /** // TODO: check the start end logic and udate the commented code  */
//        $exam = Exam::findOrFail($examData['id']);
//        $examStartTime = CarbonImmutable::create($exam->exam_date);
//        $examEndTime = $examStartTime->addMinutes($exam->duration);
//        throw_if($this->startTime->lt($examStartTime), ValidationException::withMessages(["Exam has not started"]));
//        throw_if($this->startTime->gt($examEndTime), ValidationException::withMessages(["Exam is over"]));

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

        /** // TODO: check the start end logic and udate the commented code  */
//        $exam = Exam::findOrFail($examData['id']);
//        $examStartTime = CarbonImmutable::create($exam->exam_date);
//        $examEndTime = $examStartTime->addMinutes($exam->duration);
//        throw_if($this->startTime->lt($examStartTime), ValidationException::withMessages(["Exam has not started"]));
//        throw_if($this->startTime->gt($examEndTime), ValidationException::withMessages(["Exam is over"]));


        try {
            $this->examService->submitExamQuestionPaper($validatedData);
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
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function youthExamMarkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateYouthExam', Exam::class);
        $validatedData = $this->examService->youthExamMarkUpdateValidator($request)->validate();
        $this->examService->youthExamMarkUpdate($validatedData);
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
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function examPublish(Request $request, int $id): JsonResponse
    {
        $this->authorize('create', Exam::class);
        $examType = ExamType::findOrFail($id);
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
        $batchIds = app(BatchService::class)->getBatchIdByFourIrInitiativeId($fourIrInitiativeId);
        $request->offsetSet('batch_id', $batchIds);
        $filter = $this->examService->youthAssessmentValidator($request)->validate();
        $youthAssessmentList = $this->examService->getYouthAssessmentList($filter);
        $response = [
            "data" => $youthAssessmentList,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth Assessment List",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

}
