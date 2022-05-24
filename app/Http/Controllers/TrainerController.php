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
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Trainer::class);

        $filter = $this->trainerService->filterValidator($request)->validate();

        $response = $this->trainerService->getTrainerList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $trainer = $this->trainerService->getOneTrainer($id);

        $this->authorize('view', $trainer);

        $response = [
            "data" => $trainer,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
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
        $this->authorize('create', Trainer::class);

        $validatedData = $this->trainerService->validator($request)->validate();
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
        $trainer = Trainer::findOrFail($id);

        $this->authorize('update', $trainer);

        $validated = $this->trainerService->validator($request, $id)->validate();

        $data = $this->trainerService->update($trainer, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Trainer updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $trainer = Trainer::findOrFail($id);

        $this->authorize('delete', $trainer);

        $this->trainerService->destroy($trainer);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Trainer deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->trainerService->getTrainerTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $trainer = Trainer::onlyTrashed()->findOrFail($id);
        $this->trainerService->restore($trainer);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Trainer restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    public function forceDelete(int $id): JsonResponse
    {
        $trainer = Trainer::onlyTrashed()->findOrFail($id);
        $this->trainerService->forceDelete($trainer);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Trainer permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
