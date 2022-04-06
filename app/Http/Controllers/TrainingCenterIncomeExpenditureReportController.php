<?php

namespace App\Http\Controllers;

use App\Models\TrainingCenterIncomeExpenditureReport;
use App\Models\TrainingCenterProgressReport;
use App\Services\TrainingCenterIncomeExpenditureReportService;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TrainingCenterIncomeExpenditureReportController extends Controller
{
    /**
     * @var TrainingCenterIncomeExpenditureReportService
     */
    public TrainingCenterIncomeExpenditureReportService $trainingCenterIncomeExpenditureReportService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * TrainingCenterController constructor.
     * @param TrainingCenterIncomeExpenditureReportService $trainingCenterIncomeExpenditureReportService
     */
    public function __construct(TrainingCenterIncomeExpenditureReportService $trainingCenterIncomeExpenditureReportService)
    {
        $this->trainingCenterIncomeExpenditureReportService = $trainingCenterIncomeExpenditureReportService;
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
        $this->authorize('viewAny',TrainingCenterProgressReport::class);
        $filter = $this->trainingCenterIncomeExpenditureReportService->filterValidator($request)->validate();

        $response = $this->trainingCenterIncomeExpenditureReportService->TrainingCenterIncomeExpenditureReport($filter, $this->startTime);
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
        $this->authorize('view',TrainingCenterIncomeExpenditureReport::class);
        $data = $this->trainingCenterIncomeExpenditureReportService->getOneTrainingCenterIncomeExpenditureReport($id);
        $this->authorize('viewAny',TrainingCenterProgressReport::class);
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
        $this->authorize('create',TrainingCenterIncomeExpenditureReport::class);
        $validatedData = $this->trainingCenterIncomeExpenditureReportService->validator($request)->validate();

        $data = $this->trainingCenterIncomeExpenditureReportService->store($validatedData);
        $this->authorize('viewAny',TrainingCenterProgressReport::class);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Training center Income Expenditure Report added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

}
