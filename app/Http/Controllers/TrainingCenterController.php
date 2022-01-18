<?php

namespace App\Http\Controllers;

use App\Services\CommonServices\CodeGeneratorService;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\TrainingCenterService;

/**
 * Class TrainingCenterController
 * @package App\Http\Controllers
 */
class TrainingCenterController extends Controller
{
    /**
     * @var TrainingCenterService
     */
    public TrainingCenterService $trainingCenterService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * TrainingCenterController constructor.
     * @param TrainingCenterService $trainingCenterService
     */
    public function __construct(TrainingCenterService $trainingCenterService)
    {
        $this->trainingCenterService = $trainingCenterService;
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
        $this->authorize('viewAny', TrainingCenter::class);
        $filter = $this->trainingCenterService->filterValidator($request)->validate();

        $response = $this->trainingCenterService->getTrainingCenterList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     *  * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $data = $this->trainingCenterService->getOneTrainingCenter($id);
        $this->authorize('view', $data);
        $response = [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     *  * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', TrainingCenter::class);
        $validatedData = $this->trainingCenterService->validator($request)->validate();

        $validatedData['code'] = CodeGeneratorService::getTrainingCenterCode($validatedData['institute_id']);

        $data = $this->trainingCenterService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Training center added successfully.",
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
        $trainingCenter = TrainingCenter::findOrFail($id);
        $this->authorize('update', $trainingCenter);
        $validated = $this->trainingCenterService->validator($request, $id)->validate();
        $data = $this->trainingCenterService->update($trainingCenter, $validated);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Training center updated successfully.",
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
        $trainingCenter = TrainingCenter::findOrFail($id);
        $this->authorize('delete', $trainingCenter);

        DB::beginTransaction();
        try {
            $this->trainingCenterService->destroy($trainingCenter);
            $this->trainingCenterService->trainingCenterUserDestroy($trainingCenter);

            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Training center deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->trainingCenterService->getTrainingCenterTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $trainingCenter = TrainingCenter::onlyTrashed()->findOrFail($id);
        $this->trainingCenterService->restore($trainingCenter);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Training Center restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $trainingCenter = TrainingCenter::onlyTrashed()->findOrFail($id);
        $this->trainingCenterService->forceDelete($trainingCenter);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Training Center permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws ValidationException
     */
    public function getTrainingCentersWithFilters(Request $request): JsonResponse
    {
        $filter = $this->trainingCenterService->filterValidator($request)->validate();

        $response = $this->trainingCenterService->getPublicTrainingCenterList($filter, $this->startTime);
        return Response::json($response);
    }

}
