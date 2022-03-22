<?php

namespace App\Services;

use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use App\Models\Institute;
use App\Models\Skill;
use App\Models\TrainingCenterIncomeExpenditureReport;
use App\Models\User;
use App\Services\CommonServices\CodeGeneratorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class TrainingCenterService
 * @package App\Services
 */
class TrainingCenterIncomeExpenditureReportService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function TrainingCenterIncomeExpenditureReport(array $request, Carbon $startTime): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var TrainingCenter|Builder $trainingCenterIncomeExpenditureBuilder */
        $trainingCenterIncomeExpenditureBuilder = TrainingCenterIncomeExpenditureReport::select([
            '$training_center_income_expenditure_reports.id',
            '$training_center_income_expenditure_reports.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            '$training_center_income_expenditure_reports.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            '$training_center_income_expenditure_reports.reporting_month',
            '$training_center_income_expenditure_reports.trade_name',
            '$training_center_income_expenditure_reports.number_of_labs_or_training_rooms',
            '$training_center_income_expenditure_reports.number_of_allowed_seats',
            '$training_center_income_expenditure_reports.number_of_trainees',
            '$training_center_income_expenditure_reports.course_fee_per_trainee',
            '$training_center_income_expenditure_reports.total_course_income_from_course_fee',
            '$training_center_income_expenditure_reports.total_course_income_from_application_and_others',
            '$training_center_income_expenditure_reports.total_course_income_from_total_income',
            '$training_center_income_expenditure_reports.reporting_month_income',
            '$training_center_income_expenditure_reports.reporting_month_training_expenses_instructor_salaries',
            '$training_center_income_expenditure_reports.reporting_month_training_expenses_other_expenses',
            '$training_center_income_expenditure_reports.reporting_month_training_expenses_total_expenses',
            '$training_center_income_expenditure_reports.reporting_month_total_income',
            '$training_center_income_expenditure_reports.bank_status_up_to_previous_month',
            '$training_center_income_expenditure_reports.bank_status_so_far',
            '$training_center_income_expenditure_reports.comments',
            '$training_center_income_expenditure_reports.created_at',
            '$training_center_income_expenditure_reports.updated_at',
        ])->acl();

        $trainingCenterIncomeExpenditureBuilder->join("institutes", function ($join) {
            $join->on('$training_center_income_expenditure_reports.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterIncomeExpenditureBuilder->join("branches", function ($join) {
            $join->on('$training_center_income_expenditure_reports.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $trainingCenterIncomeExpenditureBuilder->orderBy('$training_center_income_expenditure_reports.id', $order);

        if (is_numeric($instituteId)) {
            $trainingCenterIncomeExpenditureBuilder->where('$training_center_income_expenditure_reports.institute_id', '=', $instituteId);
        }
        if (is_numeric($trainingCenterId)) {
            $trainingCenterIncomeExpenditureBuilder->where('$training_center_income_expenditure_reports.training_center_id', '=', $trainingCenterId);
        }

        /** @var Collection $trainingCenterIncomeExpenditureBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainingCenterIncomeExpenditureReports = $trainingCenterIncomeExpenditureBuilder->paginate($pageSize);
            $paginateData = (object)$trainingCenterIncomeExpenditureReports->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenterIncomeExpenditureReports = $trainingCenterIncomeExpenditureBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainingCenterIncomeExpenditureReports->toArray()['data'] ?? $trainingCenterIncomeExpenditureReports->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }


    /**
     * @param int $id
     * @return Model|Builder
     */
    public function getOneTrainingCenterIncomeExpenditureReport(int $id): Model|Builder
    {
        /** @var TrainingCenter|Builder $trainingCenterIncomeExpenditureBuilder */
        $trainingCenterIncomeExpenditureBuilder = TrainingCenterIncomeExpenditureReport::select([

            '$training_center_income_expenditure_reports.id',
            '$training_center_income_expenditure_reports.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            '$training_center_income_expenditure_reports.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            '$training_center_income_expenditure_reports.reporting_month',
            '$training_center_income_expenditure_reports.trade_name',
            '$training_center_income_expenditure_reports.number_of_labs_or_training_rooms',
            '$training_center_income_expenditure_reports.number_of_allowed_seats',
            '$training_center_income_expenditure_reports.number_of_trainees',
            '$training_center_income_expenditure_reports.course_fee_per_trainee',
            '$training_center_income_expenditure_reports.total_course_income_from_course_fee',
            '$training_center_income_expenditure_reports.total_course_income_from_application_and_others',
            '$training_center_income_expenditure_reports.total_course_income_from_total_income',
            '$training_center_income_expenditure_reports.reporting_month_income',
            '$training_center_income_expenditure_reports.reporting_month_training_expenses_instructor_salaries',
            '$training_center_income_expenditure_reports.reporting_month_training_expenses_other_expenses',
            '$training_center_income_expenditure_reports.reporting_month_training_expenses_total_expenses',
            '$training_center_income_expenditure_reports.reporting_month_total_income',
            '$training_center_income_expenditure_reports.bank_status_up_to_previous_month',
            '$training_center_income_expenditure_reports.bank_status_so_far',
            '$training_center_income_expenditure_reports.comments',
            '$training_center_income_expenditure_reports.created_at',
            '$training_center_income_expenditure_reports.updated_at',
        ]);

        $trainingCenterIncomeExpenditureBuilder->join("institutes", function ($join) {
            $join->on('$training_center_income_expenditure_reports.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterIncomeExpenditureBuilder->leftJoin("training_centers", function ($join) {
            $join->on('$training_center_income_expenditure_reports.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });
        $trainingCenterIncomeExpenditureBuilder->where('$training_center_income_expenditure_reports.id', $id);

        return $trainingCenterIncomeExpenditureBuilder->firstOrFail();

    }


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
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'training_center_id' => 'nullable|int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]

        ];

        return Validator::make($request->all(), $rules, $customMessage);
    }
}
