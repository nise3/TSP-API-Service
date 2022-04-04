<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
    public ExamService $ExamService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * @param ExamService $ExamService
     */

    public function __construct(ExamService $ExamService)
    {
        $this->ExamService = $ExamService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    public function getList(Request $request): JsonResponse
    {

        $filter = $this->ExamService->filterValidator($request)->validate();
        $response = $this->ExamService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */

    public function read(int $id): JsonResponse
    {
        $exam = $this->ExamService->getOneexam($id);
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
        $validatedData = $this->ExamService->validator($request)->validate();
        $examType = $this->ExamService->storeExamType($validatedData);
        $validatedData['exam_type_id'] = $examType->id;
        $exam = $this->ExamService->storeExam($validatedData);

        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED) {
            $validatedData['exam_ids'] = $exam;
        } else {
            $validatedData['exam_ids'] = $exam->id;
        }

        if (!empty($validatedData['sets']) || !empty($validatedData['offline']['sets'])) {
            $examSets = $this->ExamService->storeExamSets($validatedData);
            $validatedData['exam_sets'] = $examSets;
        }

        if (!empty($validatedData['exam_questions']) || !empty($validatedData['online']['exam_questions']) || !empty($validatedData['online']['exam_questions'])) {
            $this->ExamService->storeExamSections($validatedData);
        }

        //TODO :complete exam creation flow
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Exam added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
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

        $validated = $this->ExamService->validator($request, $id)->validate();

        $data = $this->ExamService->update($exam, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Exam Subject updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */

    public function destroy(int $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);
        $this->ExamService->destroy($exam);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Exam Subject deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
