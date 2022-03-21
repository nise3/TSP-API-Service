<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\TrainingCenterSkillDevelopmentReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Lumen\Application;
use Symfony\Component\HttpFoundation\Response;

class TrainingCenterSkillDevelopmentReportService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainingCenterSkillDevelopmentReportList(array $request, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var Builder|TrainingCenterSkillDevelopmentReport $trainingCenterSKillReportBuilder */
        $trainingCenterSKillReportBuilder = TrainingCenterSkillDevelopmentReport::select([
            'training_center_skill_development_reports.id',
            'training_center_skill_development_reports.institute_id',
            'training_center_skill_development_reports.training_center_id',
            'training_center_skill_development_reports.reporting_month',
            'training_center_skill_development_reports.number_of_trades_allowed',
            'training_center_skill_development_reports.number_of_ongoing_trades',
            'training_center_skill_development_reports.number_of_computer',
            'training_center_skill_development_reports.number_of_other_equipment',
            'training_center_skill_development_reports.amount_of_total_fdr',
            'training_center_skill_development_reports.current_session_trainees_women',
            'training_center_skill_development_reports.current_session_trainees_men',
            'training_center_skill_development_reports.current_session_trainees_disabled_and_others',
            'training_center_skill_development_reports.current_session_trainees_total',
            'training_center_skill_development_reports.total_trainees_women',
            'training_center_skill_development_reports.total_trainees_men',
            'training_center_skill_development_reports.total_trainees_women',
            'training_center_skill_development_reports.total_trainees_disabled_and_others',
            'training_center_skill_development_reports.total_trainees_total',
            'training_center_skill_development_reports.bank_status_skill_development',
            'training_center_skill_development_reports.bank_status_coordinating_council',
            'training_center_skill_development_reports.date_of_last_election_of_all_party_council',
            'training_center_skill_development_reports.created_at',
            'training_center_skill_development_reports.updated_at',
        ])->acl();

        $trainingCenterSKillReportBuilder->join("institutes", function ($join) {
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterSKillReportBuilder->join("training_centers", function ($join) {
            $join->on('training_centers.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $trainingCenterSKillReportBuilder->orderBy('training_center_skill_development_reports.id', $order);

        if (is_numeric($instituteId)) {
            $trainingCenterSKillReportBuilder->where('training_center_skill_development_reports.institute_id', '=', $instituteId);
        }
        if (is_numeric($trainingCenterId)) {
            $trainingCenterSKillReportBuilder->where('training_center_skill_development_reports.training_center_id', '=', $trainingCenterId);
        }

        /** @var Collection $trainingCenterSkillReports */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainingCenterSkillReports = $trainingCenterSKillReportBuilder->paginate($pageSize);
            $paginateData = (object)$trainingCenterSkillReports->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenterSkillReports = $trainingCenterSKillReportBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainingCenterSkillReports->toArray()['data'] ?? $trainingCenterSkillReports->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;

    }

    /**
     * @param array $data
     * @return TrainingCenterSkillDevelopmentReport
     */
    public function store(array $data): TrainingCenterSkillDevelopmentReport
    {
        $data['current_session_trainees_total'] = $data['current_session_trainees_women'] + $data['current_session_trainees_men'] + $data['current_session_trainees_disabled_and_others'];
        $data['total_trainees_total'] = $data['total_trainees_women'] + $data['total_trainees_men'] + $data['total_trainees_disabled_and_others'];
        $trainingCenterSkillDevelopmentReport = app(TrainingCenterSkillDevelopmentReport::class);
        $trainingCenterSkillDevelopmentReport->fill($data);
        $trainingCenterSkillDevelopmentReport->save();

        return $trainingCenterSkillDevelopmentReport;
    }

    /**
     * @param int $id
     * @return Model|Builder
     */
    public function getOneTrainingCenterSkillDevelopmentReport(int $id): Model|Builder
    {
        /** @var Builder|TrainingCenterSkillDevelopmentReport $trainingCenterSKillReportBuilder */
        $trainingCenterSKillReportBuilder = TrainingCenterSkillDevelopmentReport::select([
            'training_center_skill_development_reports.id',
            'training_center_skill_development_reports.institute_id',
            'training_center_skill_development_reports.training_center_id',
            'training_center_skill_development_reports.reporting_month',
            'training_center_skill_development_reports.number_of_trades_allowed',
            'training_center_skill_development_reports.number_of_ongoing_trades',
            'training_center_skill_development_reports.number_of_computer',
            'training_center_skill_development_reports.number_of_other_equipment',
            'training_center_skill_development_reports.amount_of_total_fdr',
            'training_center_skill_development_reports.current_session_trainees_women',
            'training_center_skill_development_reports.current_session_trainees_men',
            'training_center_skill_development_reports.current_session_trainees_disabled_and_others',
            'training_center_skill_development_reports.current_session_trainees_total',
            'training_center_skill_development_reports.total_trainees_women',
            'training_center_skill_development_reports.total_trainees_men',
            'training_center_skill_development_reports.total_trainees_women',
            'training_center_skill_development_reports.total_trainees_disabled_and_others',
            'training_center_skill_development_reports.total_trainees_total',
            'training_center_skill_development_reports.bank_status_skill_development',
            'training_center_skill_development_reports.bank_status_coordinating_council',
            'training_center_skill_development_reports.date_of_last_election_of_all_party_council',
            'training_center_skill_development_reports.created_at',
            'training_center_skill_development_reports.updated_at',
        ]);

        $trainingCenterSKillReportBuilder->join("institutes", function ($join) {
            $join->on('training_center_skill_development_reports.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterSKillReportBuilder->join("training_centers", function ($join) {
            $join->on('training_center_skill_development_reports.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $trainingCenterSKillReportBuilder->where('training_center_skill_development_reports.id', '=', $id);


        return $trainingCenterSKillReportBuilder->firstOrFail();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
        ];

        $rules = [
            'institute_id' => 'int|gt:0',
            'training_center_id' => 'nullable|int|gt:0',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]

        ];

        return Validator::make($request->all(), $rules, $customMessage);

    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
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
                'numeric',
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
                'string',
            ],
            'bank_status_coordinating_council' => [
                'nullable',
                'string',
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

        return Validator::make($data, $rules, $customMessage);
    }


}
