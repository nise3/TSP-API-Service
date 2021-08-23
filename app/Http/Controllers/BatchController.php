<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\BatchService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class BatcheController
 * @package App\Http\Controllers
 */
class BatchController extends Controller
{
    /**
     * @var BatchService
     */
    public BatchService $batchService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon $startTime;

    /**
     * BatcheController constructor.
     * @param BatchService $batchService
     */
    public function __construct(BatchService $batchService)
    {
        $this->batchService = $batchService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->batchService->getBatchList($request,  $this->startTime);
        } catch (Throwable $e) {
            return  $e;
        }
        return Response::json($response);
    }

    /**
     * @param int $id
     *  * Display the specified resource
     *  @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->batchService->getBatch($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->batchService->validator($request)->validate();
        try {
            $data = $this->batchService->store($validatedData);
            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Batch added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);
        $validated = $this->batchService->validator($request)->validate();
        try {
            $data = $this->batchService->update($batch, $validated);
            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Batch update successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);

        try {
            $this->batchService->destroy($batch);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Batch Delete successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
