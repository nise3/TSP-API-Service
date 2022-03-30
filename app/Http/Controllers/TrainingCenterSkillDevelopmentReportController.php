<?php

namespace App\Http\Controllers;

use App\Models\TrainingCenterProgressReport;
use App\Models\TrainingCenterSkillDevelopmentReport;
use App\Services\TrainingCenterSkillDevelopmentReportService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
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
        $this->authorize('viewAny',TrainingCenterProgressReport::class);
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
        $this->authorize('view',TrainingCenterProgressReport::class);
        $data = $this->trainingCenterSkillDevelopmentReportService->getOneTrainingCenterSkillDevelopmentReport($id);
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
        $this->authorize('create',TrainingCenterProgressReport::class);
        $validatedData = $this->trainingCenterSkillDevelopmentReportService->validator($request)->validate();

        $data = $this->trainingCenterSkillDevelopmentReportService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Training center Skill development Report added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


}
