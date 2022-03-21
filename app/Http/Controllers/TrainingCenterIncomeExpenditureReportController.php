<?php

namespace App\Http\Controllers;

use App\Models\TrainingCenterIncomeExpenditureReport;
use App\Services\TrainingCenterIncomeExpenditureReportService;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $this->authorize('viewAny', TrainingCenterIncomeExpenditureReport::class);
        $filter = $this->trainingCenterIncomeExpenditureReportService->filterValidator($request)->validate();

        $response = $this->trainingCenterService->getTrainingCenterList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
