<?php

namespace App\Http\Controllers;

use App\Models\RplQuestionBank;
use App\Services\RplQuestionBankService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplQuestionBankController extends Controller
{
    /**
     * @var RplQuestionBankService
     */
    public RplQuestionBankService $rplQuestionBankService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplQuestionBankController constructor.
     * @param RplQuestionBankService $rplQuestionBankService
     */

    public function __construct(RplQuestionBankService $rplQuestionBankService)
    {
        $this->rplQuestionBankService = $rplQuestionBankService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RplQuestionBank::class);

        $filter = $this->rplQuestionBankService->filterValidator($request)->validate();

        $response = $this->rplQuestionBankService->getQuestionBankList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rplQuestionBankService->filterValidator($request)->validate();

        $response = $this->rplQuestionBankService->getQuestionBankList($filter, $this->startTime,false);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * * Display the specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $questionBank = $this->rplQuestionBankService->getOneQuestionBank($id);
        $this->authorize('view', $questionBank);

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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', RplQuestionBank::class);

        $validated = $this->rplQuestionBankService->validator($request)->validate();
        $questionBank = $this->rplQuestionBankService->store($validated);

        $response = [
            'data' => $questionBank,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Question Bank added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $questionBank = RplQuestionBank::findOrFail($id);

        $this->authorize('update', $questionBank);

        $validated = $this->rplQuestionBankService->validator($request, $id)->validate();
        $data = $this->rplQuestionBankService->update($questionBank, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Question Bank updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $questionBank = RplQuestionBank::findOrFail($id);

        $this->authorize('delete', $questionBank);

        DB::beginTransaction();
        try {
            $this->rplQuestionBankService->destroy($questionBank);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Question Bank deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
