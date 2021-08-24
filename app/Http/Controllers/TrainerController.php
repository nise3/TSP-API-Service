<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Services\TrainerService;
use Exception;
use \Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TrainerController extends Controller
{
    /**
     * @var TrainerService
     */
    public TrainerService $trainerService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * TrainerController constructor.
     * @param TrainerService $trainerService
     */

    public function __construct(TrainerService $trainerService)
    {
        $this->trainerService = $trainerService;
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
            $response = $this->trainerService->getTrainerList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * Display the specified resource
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->trainerService->getOneTrainer($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->trainerService->validator($request)->validate();
        try {
            $data = $this->trainerService->store($validatedData);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Trainer added successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $trainer = Trainer::findOrFail($id);
        $validated = $this->trainerService->validator($request, $id)->validate();

        try {
            $data = $this->trainerService->update($trainer, $validated);
            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Trainer updated successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $trainer = Trainer::findOrFail($id);
        try {
            $this->trainerService->destroy($trainer);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Trainer deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function assignTrainerToBatch(Request $request, int $id): JsonResponse
    {
        $trainer = Trainer::findOrFail($id);
        $validated = $this->trainerService->batchValidator($request)->validated();
        try {
            $trainer = $this->trainerService->assignTrainer($trainer, $validated['batchIds']);
            $response = [
                'data' => $trainer,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "trainer added to batch successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
