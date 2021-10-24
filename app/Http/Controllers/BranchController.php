<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\BranchService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class BranchController
 * @package App\Http\Controllers
 */
class BranchController extends Controller
{
    /**
     * @var BranchService
     */
    public BranchService $branchService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * BranchController constructor.
     * @param BranchService $branchService
     */
    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * * @return Exception|JsonResponse|Throwable
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->branchService->filterValidator($request)->validate();

        $response = $this->branchService->getBranchList($filter, $this->startTime);

        return Response::json($response);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read($id): JsonResponse
    {
        $response = $this->branchService->getOneBranch($id, $this->startTime);
        return Response::json($response);

    }

    /**
     *  * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->branchService->validator($request)->validate();
        $data = $this->branchService->store($validatedData);
        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Branch added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);

        $validated = $this->branchService->validator($request)->validate();

        $data = $this->branchService->update($branch, $validated);

        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Brnach Update successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     *  *  remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);

        $this->branchService->destroy($branch);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Branch deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->branchService->getBranchTrashList($request, $this->startTime);
        return Response::json($response);
    }

    public function restore(int $id): JsonResponse
    {
        $branch = Branch::onlyTrashed()->findOrFail($id);
        $this->branchService->restore($branch);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Branch restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Throwable
     */
    public function forceDelete(int $id): JsonResponse
    {
        $branch = Branch::onlyTrashed()->findOrFail($id);
        $this->branchService->forceDelete($branch);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Branch permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
