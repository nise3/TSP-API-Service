<?php

namespace App\Http\Controllers;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->trainingCenterService->getTrainingCenterList($request, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->trainingCenterService->getOneTrainingCenter($id, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
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
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
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
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return JsonResponse
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
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_OK);
    }
}
