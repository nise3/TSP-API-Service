<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\TrainingCenterCombinedProgressReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Lumen\Application;
use Symfony\Component\HttpFoundation\Response;

class TrainingCenterCombinedProgressReportService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainingCenterCombinedProgressReportList(array $request, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var Builder|TrainingCenterCombinedProgressReport $trainingCenterSKillReportBuilder */
        $trainingCenterSKillReportBuilder = TrainingCenterCombinedProgressReport::select([
            'training_center_combined_progress_reports.id',
            'training_center_combined_progress_reports.institute_id',
            'training_center_combined_progress_reports.training_center_id',
            'training_center_combined_progress_reports.reporting_month',
            'training_center_combined_progress_reports.voluntary_organizations_registered_in_current_month',
            'training_center_combined_progress_reports.members_up_to_previous_month_general_members',
            'training_center_combined_progress_reports.members_up_to_previous_month_life_member',
            'training_center_combined_progress_reports.members_up_to_previous_month_patron_member',
            'training_center_combined_progress_reports.members_up_to_previous_month_total',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_general_members',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_life_member',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_patron_member',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_total',
            'training_center_combined_progress_reports.total_number_of_members',
            'training_center_combined_progress_reports.subscriptions_collected_so_far',
            'training_center_combined_progress_reports.subscriptions_collected_in_current_month_organization',
            'training_center_combined_progress_reports.subscriptions_collected_in_current_month_member',
            'training_center_combined_progress_reports.subscriptions_collected_in_current_month_total',
            'training_center_combined_progress_reports.grants_received_in_current_month_source',
            'training_center_combined_progress_reports.grants_received_in_current_month_amount',
            'training_center_combined_progress_reports.grants_received_in_current_month_total',
            'training_center_combined_progress_reports.gross_income',
            'training_center_combined_progress_reports.income_in_skills_development_sector_trades',
            'training_center_combined_progress_reports.income_in_skills_development_sector_money',
            'training_center_combined_progress_reports.expenditure_in_skill_development_training',
            'training_center_combined_progress_reports.expenditure_in_other_sectors',
            'training_center_combined_progress_reports.expenditure_total',
            'training_center_combined_progress_reports.total_income_in_the_training_sector',
            'training_center_combined_progress_reports.bank_status_and_account_number',
            'training_center_combined_progress_reports.bank_interest',
            'training_center_combined_progress_reports.amount_of_fdr_and_bank_account_number',
            'training_center_combined_progress_reports.number_of_meetings_held_during_current_financial_year',
            'training_center_combined_progress_reports.number_of_executive_council_meetings_in_current_month',
            'training_center_combined_progress_reports.names_and_numbers_of_other_meetings',
            'training_center_combined_progress_reports.coordinating_council_meeting_total',
            'training_center_combined_progress_reports.other_activities_undertaken',
            'training_center_combined_progress_reports.created_at',
            'training_center_combined_progress_reports.updated_at',
        ])->acl();

        $trainingCenterSKillReportBuilder->join("institutes", function ($join) {
            $join->on('training_center_combined_progress_reports.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterSKillReportBuilder->join("training_centers", function ($join) {
            $join->on('training_center_combined_progress_reports.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $trainingCenterSKillReportBuilder->orderBy('training_center_combined_progress_reports.id', $order);

        if (is_numeric($instituteId)) {
            $trainingCenterSKillReportBuilder->where('training_center_combined_progress_reports.institute_id', '=', $instituteId);
        }
        if (is_numeric($trainingCenterId)) {
            $trainingCenterSKillReportBuilder->where('training_center_combined_progress_reports.training_center_id', '=', $trainingCenterId);
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
     * @return TrainingCenterCombinedProgressReport
     */
    public function store(array $data): TrainingCenterCombinedProgressReport
    {
        $data['members_up_to_previous_month_total'] =
            ($data['members_up_to_previous_month_general_members'] ?? 0) +
            ($data['members_up_to_previous_month_life_member'] ?? 0) +
            ($data['members_up_to_previous_month_patron_member'] ?? 0);

        $data['member_enrollment_in_reporting_month_total'] =
            ($data['member_enrollment_in_reporting_month_general_members'] ?? 0) +
            ($data['member_enrollment_in_reporting_month_life_member'] ?? 0) +
            ($data['member_enrollment_in_reporting_month_patron_member'] ?? 0);

        $data['total_number_of_members'] =
            ($data['members_up_to_previous_month_total'] ?? 0) +
            ($data['member_enrollment_in_reporting_month_total'] ?? 0);

        $data['subscriptions_collected_in_current_month_total'] =
            ($data['subscriptions_collected_in_current_month_organization'] ?? 0) +
            ($data['subscriptions_collected_in_current_month_member'] ?? 0);

        $data['grants_received_in_current_month_total'] =
            ($data['grants_received_in_current_month_source'] ?? 0) +
            ($data['grants_received_in_current_month_amount'] ?? 0);

        $data['expenditure_total'] =
            ($data['expenditure_in_skill_development_training'] ?? 0) +
            ($data['expenditure_in_other_sectors'] ?? 0);


        $trainingCenterCombinedProgressReport = app(TrainingCenterCombinedProgressReport::class);
        $trainingCenterCombinedProgressReport->fill($data);
        $trainingCenterCombinedProgressReport->save();

        return $trainingCenterCombinedProgressReport;
    }

    /**
     * @param int $id
     * @return Model|Builder
     */
    public function getOneTrainingCenterCombinedProgressReport(int $id): Model|Builder
    {
        /** @var Builder|TrainingCenterCombinedProgressReport $trainingCenterSKillReportBuilder */
        $trainingCenterSKillReportBuilder = TrainingCenterCombinedProgressReport::select([
            'training_center_combined_progress_reports.id',
            'training_center_combined_progress_reports.institute_id',
            'training_center_combined_progress_reports.training_center_id',
            'training_center_combined_progress_reports.reporting_month',
            'training_center_combined_progress_reports.voluntary_organizations_registered_in_current_month',
            'training_center_combined_progress_reports.members_up_to_previous_month_general_members',
            'training_center_combined_progress_reports.members_up_to_previous_month_life_member',
            'training_center_combined_progress_reports.members_up_to_previous_month_patron_member',
            'training_center_combined_progress_reports.members_up_to_previous_month_total',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_general_members',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_life_member',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_patron_member',
            'training_center_combined_progress_reports.member_enrollment_in_reporting_month_total',
            'training_center_combined_progress_reports.total_number_of_members',
            'training_center_combined_progress_reports.subscriptions_collected_so_far',
            'training_center_combined_progress_reports.subscriptions_collected_in_current_month_organization',
            'training_center_combined_progress_reports.subscriptions_collected_in_current_month_member',
            'training_center_combined_progress_reports.subscriptions_collected_in_current_month_total',
            'training_center_combined_progress_reports.grants_received_in_current_month_source',
            'training_center_combined_progress_reports.grants_received_in_current_month_amount',
            'training_center_combined_progress_reports.grants_received_in_current_month_total',
            'training_center_combined_progress_reports.gross_income',
            'training_center_combined_progress_reports.income_in_skills_development_sector_trades',
            'training_center_combined_progress_reports.income_in_skills_development_sector_money',
            'training_center_combined_progress_reports.expenditure_in_skill_development_training',
            'training_center_combined_progress_reports.expenditure_in_other_sectors',
            'training_center_combined_progress_reports.expenditure_total',
            'training_center_combined_progress_reports.total_income_in_the_training_sector',
            'training_center_combined_progress_reports.bank_status_and_account_number',
            'training_center_combined_progress_reports.bank_interest',
            'training_center_combined_progress_reports.amount_of_fdr_and_bank_account_number',
            'training_center_combined_progress_reports.number_of_meetings_held_during_current_financial_year',
            'training_center_combined_progress_reports.number_of_executive_council_meetings_in_current_month',
            'training_center_combined_progress_reports.names_and_numbers_of_other_meetings',
            'training_center_combined_progress_reports.coordinating_council_meeting_total',
            'training_center_combined_progress_reports.other_activities_undertaken',
            'training_center_combined_progress_reports.created_at',
            'training_center_combined_progress_reports.updated_at',
        ]);

        $trainingCenterSKillReportBuilder->join("institutes", function ($join) {
            $join->on('training_center_combined_progress_reports.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterSKillReportBuilder->join("training_centers", function ($join) {
            $join->on('training_center_combined_progress_reports.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $trainingCenterSKillReportBuilder->where('training_center_combined_progress_reports.id', '=', $id);


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
            'institute_id' => 'required|int|gt:0',
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

            'voluntary_organizations_registered_in_current_month' => 'nullable|int|min:0',
            'members_up_to_previous_month_general_members' => 'nullable|int|min:0',
            'members_up_to_previous_month_life_member' => 'nullable|int|min:0',
            'members_up_to_previous_month_patron_member' => 'nullable|int|min:0',
            'members_up_to_previous_month_total' => 'nullable|int|min:0',
            'member_enrollment_in_reporting_month_general_members' => 'nullable|int|min:0',
            'member_enrollment_in_reporting_month_life_member' => 'nullable|int|min:0',
            'member_enrollment_in_reporting_month_patron_member' => 'nullable|int|min:0',
            'member_enrollment_in_reporting_month_total' => 'nullable|int|min:0',
            'total_number_of_members' => 'nullable|int|min:0',
            'subscriptions_collected_so_far' => 'nullable|numeric|min:0',
            'subscriptions_collected_in_current_month_organization' => 'nullable|numeric|min:0',
            'subscriptions_collected_in_current_month_member' => 'nullable|numeric|min:0',
            'subscriptions_collected_in_current_month_total' => 'nullable|numeric|min:0',
            'grants_received_in_current_month_source' => 'nullable|string',
            'grants_received_in_current_month_amount' => 'nullable|numeric|min:0',
            'grants_received_in_current_month_total' => 'nullable|numeric|min:0',
            'gross_income' => 'nullable|numeric|min:0',

            'income_in_skills_development_sector_trades' => 'nullable|numeric|min:0',
            'income_in_skills_development_sector_money' => 'nullable|numeric|min:0',
            'expenditure_in_skill_development_training' => 'nullable|numeric|min:0',
            'expenditure_in_other_sectors' => 'nullable|numeric|min:0',
            'expenditure_total' => 'nullable|numeric|min:0',
            'total_income_in_the_training_sector' => 'nullable|numeric|min:0',
            'bank_status_and_account_number' => 'nullable|string',
            'bank_interest' => 'nullable|numeric|min:0',
            'amount_of_fdr_and_bank_account_number' => 'nullable|string',
            'number_of_meetings_held_during_current_financial_year' => 'nullable|int|min:0',
            'number_of_executive_council_meetings_in_current_month' => 'nullable|int|min:0',
            'names_and_numbers_of_other_meetings' => 'nullable|string',
            'coordinating_council_meeting_total' => 'nullable|int|min:0',
            'other_activities_undertaken' => 'nullable|string',

        ];

        return Validator::make($data, $rules, $customMessage);
    }


}
