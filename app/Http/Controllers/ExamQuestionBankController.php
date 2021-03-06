<?php

namespace App\Http\Controllers;

use App\Models\ExamQuestionBank;
use App\Services\ExamQuestionBankService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExamQuestionBankController extends Controller
{

    public ExamQuestionBankService $examQuestionBankService;

    private Carbon $startTime;

    /**
     * @param ExamQuestionBankService $examQuestionBankService
     */
    public function __construct(ExamQuestionBankService $examQuestionBankService)
    {
        $this->examQuestionBankService = $examQuestionBankService;
        $this->startTime = Carbon::now();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {

        $this->authorize('viewAny',ExamQuestionBank::class);
        $filter = $this->examQuestionBankService->filterValidator($request)->validate();

        $response = $this->examQuestionBankService->getQuestionBankList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $questionBank = $this->examQuestionBankService->getOneExamQuestionBank($id);
        $this->authorize('view',$questionBank);
        $response = [
            "data" => $questionBank,
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
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create',ExamQuestionBank::class);
        $validated = $this->examQuestionBankService->validator($request)->validate();
        $questionBank = $this->examQuestionBankService->store($validated);

        $response = [
            'data' => $questionBank,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Exam Question Bank added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id): JsonResponse
    {

        $examQuestionBank = ExamQuestionBank::findOrFail($id);
        $this->authorize('update',$examQuestionBank);
        $validated = $this->examQuestionBankService->validator($request, $id)->validate();
        $data = $this->examQuestionBankService->update($examQuestionBank, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Exam Question Bank updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $questionBank = ExamQuestionBank::findOrFail($id);
        $this->authorize('delete',$questionBank);
        $this->examQuestionBankService->destroy($questionBank);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Question Bank deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
