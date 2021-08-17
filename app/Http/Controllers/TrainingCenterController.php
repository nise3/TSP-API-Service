<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
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
     *  @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->trainingCenterService->getTrainingCenterList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     *  * Display the specified resource
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->trainingCenterService->getOneTrainingCenter($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     *  * Store a newly created resource in storage.
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->trainingCenterService->validator($request)->validate();
        try {
            $data = $this->trainingCenterService->store($validatedData);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Training center added successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
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
        $trainingCenter = TrainingCenter::findOrFail($id);
        $validated = $this->trainingCenterService->validator($request, $id)->validate();
        try {
            $data = $this->trainingCenterService->update($trainingCenter, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Training center updated successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $trainingCenter = TrainingCenter::findOrFail($id);
        try {
            $this->trainingCenterService->destroy($trainingCenter);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Training center deleted successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_OK);
    }
}
