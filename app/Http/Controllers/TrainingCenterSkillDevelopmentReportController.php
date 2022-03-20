<?php

namespace App\Http\Controllers;

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
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function validator(Request $request): Validator
    {
        $request->offsetSet('deleted_at', null);
        $data = $request->all();

        $customMessage = [];

        $rules = [
            'institute_id' => [
                'required',
                'int',
                'min:1',
                'exists:institutes,id,deleted_at,NULL',
            ],
            'training_center_id' => [
                'required',
                'int',
                'min:1',
                'exists:training_centers,id,deleted_at,NULL',
            ],
            'reporting_month' => [
                'required',
                'date',
            ],
            'number_of_trades_allowed' => [
                'nullable',
                'int',
                'min:0',
            ],
            'number_of_ongoing_trades' => [
                'nullable',
                'int',
                'min:0',
            ],
            'number_of_computers' => [
                'nullable',
                'int',
                'min:0',
            ],
            'number_of_other_equipments' => [
                'nullable',
                'int',
                'min:0',
            ],
            'amount_of_total_fdr' => [
                'nullable',
                'int',
                'min:0',
            ],
            'current_session_trainees_women' => [
                'nullable',
                'int',
                'min:0',
            ],
            'current_session_trainees_men' => [
                'nullable',
                'int',
                'min:0',
            ],
            'current_session_trainees_disabled_and_others' => [
                'nullable',
                'int',
                'min:0',
            ],
            'total_trainees_women' => [
                'nullable',
                'int',
                'min:0',
            ],
            'total_trainees_men' => [
                'nullable',
                'int',
                'min:0',
            ],
            'total_trainees_disabled_and_others' => [
                'nullable',
                'int',
                'min:0',
            ],
            'bank_status_skill_development' => [
                'nullable',
                'int',
                'min:0',
            ],
            'bank_status_coordinating_council' => [
                'nullable',
                'int',
                'min:0',
            ],
            'date_of_last_election_of_all_party_council' => [
                'nullable',
                'date',
            ],
            'comments' => [
                'nullable',
                'string',
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }
}
