<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\BatchService;
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
    private \Carbon\Carbon|Carbon $startTime;

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
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->batchService->filterValidator($request)->validate();
        $response = $this->batchService->getBatchList($filter, $this->startTime);
        return Response::json($response);
    }

    /**
     * @param int $id
     *  * Display the specified resource
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $response = $this->batchService->getBatch($id, $this->startTime);
        return Response::json($response);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validatedData = $this->batchService->validator($request)->validate();
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
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);
        $validated = $this->batchService->validator($request)->validate();
        $data = $this->batchService->update($batch, $validated);
        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Batch update successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);

        $this->batchService->destroy($batch);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Batch Delete successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function assignTrainerToBatch(Request $request, int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);
        $validated = $this->batchService->trainerValidator($request)->validated();
        $batch = $this->batchService->assignTrainer($batch, $validated['trainerIds']);
        $response = [
            'data' => $batch->trainers()->get(),
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "trainer added to batch successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->batchService->getBatchTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $batch = Batch::onlyTrashed()->findOrFail($id);
        $this->batchService->restore($batch);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "batch restored successfully",
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
        $institute = Batch::onlyTrashed()->findOrFail($id);
        $this->batchService->forceDelete($institute);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Batch permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Throwable
     */
    public function getBatchesByCourseId(Request $request, $id): JsonResponse
    {
        $response = $this->batchService->batchesWithTrainingCenters($request, $id, $this->startTime);

        return Response::json($response);
    }
}
