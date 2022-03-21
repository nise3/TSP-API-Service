<?php

namespace App\Http\Controllers;

use App\Services\TrainingCenterProgressReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TrainingCenterProgressReportController extends Controller
{

    public TrainingCenterProgressReportService $trainingCenterProgressReportService;

    private Carbon $startTime;


    /**
     * @param TrainingCenterProgressReportService $trainingCenterProgressReportService
     */
    public function __construct(TrainingCenterProgressReportService $trainingCenterProgressReportService)
    {
        $this->trainingCenterProgressReportService = $trainingCenterProgressReportService;
        $this->startTime = Carbon::now();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->trainingCenterProgressReportService->filterValidator($request)->validate();

        $response = $this->trainingCenterProgressReportService->getTrainingCenterProgressReportList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     *  Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $data = $this->trainingCenterProgressReportService->getOneTrainingCenterProgressReport($id);
        $response = [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->trainingCenterProgressReportService->validator($request)->validate();

        $data = $this->trainingCenterProgressReportService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Training center Progress Report added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

}
