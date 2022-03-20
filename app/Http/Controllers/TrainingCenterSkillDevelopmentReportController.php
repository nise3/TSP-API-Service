<?php

namespace App\Http\Controllers;

use App\Services\TrainingCenterSkillDevelopmentReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TrainingCenterSkillDevelopmentReportController extends Controller
{

    public TrainingCenterSkillDevelopmentReportService $trainingCenterSkillDevelopmentReportService;

    private Carbon $startTime;

    /**
     * TrainingCenterController constructor.
     * @param TrainingCenterSkillDevelopmentReportService $trainingCenterSkillDevelopmentReportService
     */
    public function __construct(TrainingCenterSkillDevelopmentReportService $trainingCenterSkillDevelopmentReportService)
    {
        $this->trainingCenterSkillDevelopmentReportService = $trainingCenterSkillDevelopmentReportService;
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
        $filter = $this->trainingCenterSkillDevelopmentReportService->filterValidator($request)->validate();

        $response = $this->trainingCenterSkillDevelopmentReportService->getTrainingCenterSkillDevelopmentReportList($filter, $this->startTime);
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
        $data = $this->trainingCenterSkillDevelopmentReportService->getOneTrainingCenterSkillDevelopmentReport($id);
        $this->authorize('view', $data);
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
}
