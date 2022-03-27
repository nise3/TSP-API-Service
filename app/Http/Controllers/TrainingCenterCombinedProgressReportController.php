<?php

namespace App\Http\Controllers;

use App\Models\TrainingCenterCombinedProgressReport;
use App\Services\TrainingCenterCombinedProgressReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TrainingCenterCombinedProgressReportController extends Controller
{

    public TrainingCenterCombinedProgressReportService $trainingCenterCombinedProgressReportService;

    private Carbon $startTime;

    /**
     * TrainingCenterCombinedProgressReportController constructor.
     * @param TrainingCenterCombinedProgressReportService $trainingCenterCombinedProgressReportService
     */
    public function __construct(TrainingCenterCombinedProgressReportService $trainingCenterCombinedProgressReportService)
    {
        $this->trainingCenterCombinedProgressReportService = $trainingCenterCombinedProgressReportService;
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
        $this->authorize('viewAny',TrainingCenterCombinedProgressReport::class);
        $filter = $this->trainingCenterCombinedProgressReportService->filterValidator($request)->validate();

        $response = $this->trainingCenterCombinedProgressReportService->getTrainingCenterCombinedProgressReportList($filter, $this->startTime);
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
        $this->authorize('view',TrainingCenterCombinedProgressReport::class);
        $data = $this->trainingCenterCombinedProgressReportService->getOneTrainingCenterCombinedProgressReport($id);
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
     *  * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create',TrainingCenterCombinedProgressReport::class);
        $validatedData = $this->trainingCenterCombinedProgressReportService->validator($request)->validate();

        $data = $this->trainingCenterCombinedProgressReportService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Training center combined progress report added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

}
