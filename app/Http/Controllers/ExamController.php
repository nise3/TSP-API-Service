<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use App\Models\ExamType;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @throws ValidationException
     */

    public function getList(Request $request): JsonResponse
    {

        $filter = $this->examService->filterValidator($request)->validate();
        $response = $this->examService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */

    public function read(int $id): JsonResponse
    {
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
            $this->examService->storeExamSections($validatedData);
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
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);
        $validated = $this->examService->validator($request, $id)->validate();
        $data = $this->examService->update($exam, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Exam  updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */

    public function destroy(int $id): JsonResponse
    {
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
     * @throws ValidationException
     */
    public function getExamYouthList(Request $request, int $id): JsonResponse
    {

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
     */
    public function getExamQuestionPaper(int $id): JsonResponse
    {
        $examData = $this->examService->getExamQuestionPaper($id);
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
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $examId
     * @param int $youthId
     * @return JsonResponse
     */
    public function previewYouthExam(int $examId, int $youthId): JsonResponse
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

    public function youthExamMarkUpdate(Request $request): JsonResponse
    {
        $validatedData = $this->examService->youthExamMarkUpdateValidator($request)->validate();
        $youthExamMarkUpdateData = $this->examService->youthExamMarkUpdate($validatedData);
        $response = [
            "data" => $youthExamMarkUpdateData ?? null,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
