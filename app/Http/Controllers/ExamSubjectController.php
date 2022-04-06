<?php

namespace App\Http\Controllers;

use App\Models\ExamSubject;
use App\Services\ExamSubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExamSubjectController extends Controller
{
    /**
     * @var ExamSubjectService
     */
    public ExamSubjectService $ExamSubjectService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * @param ExamSubjectService $ExamSubjectService
     */

    public function __construct(ExamSubjectService $examSubjectService)
    {
        $this->ExamSubjectService = $examSubjectService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function getList(Request $request): JsonResponse
    {

        $filter = $this->ExamSubjectService->filterValidator($request)->validate();
        $response = $this->ExamSubjectService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */

    public function read(Request $request, int $id): JsonResponse
    {
        $examSubject = $this->ExamSubjectService->getOneExamSubject($id);
        $response = [
            "data" => $examSubject,
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
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {

        $validatedData = $this->ExamSubjectService->validator($request)->validate();
        $data = $this->ExamSubjectService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Exam Subject added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $examSubject = ExamSubject::findOrFail($id);

        $validated = $this->ExamSubjectService->validator($request, $id)->validate();

        $data = $this->ExamSubjectService->update($examSubject, $validated);

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
        $examSubject = ExamSubject::findOrFail($id);
        $this->ExamSubjectService->destroy($examSubject);
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
