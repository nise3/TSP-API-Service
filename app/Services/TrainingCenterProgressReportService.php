<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\TrainingCenterProgressReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Lumen\Application;
use Symfony\Component\HttpFoundation\Response;


class TrainingCenterProgressReportService
{

    public function getTrainingCenterProgressReportList(array $request, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var Builder|TrainingCenterProgressReport $trainingCenterProgressReportBuilder */
        $trainingCenterProgressReportBuilder = TrainingCenterProgressReport::select([
            'training_center_progress_reports.id',
            'training_center_progress_reports.institute_id',
            'training_center_progress_reports.training_center_id',
            'training_center_progress_reports.reporting_month',
            'training_center_progress_reports.trade_name',
            'training_center_progress_reports.number_of_trainers',
            'training_center_progress_reports.number_of_labs_or_training_rooms',
            'training_center_progress_reports.number_of_computers_or_training_equipments',

            'training_center_progress_reports.admitted_trainee_men',
            'training_center_progress_reports.admitted_trainee_women',
            'training_center_progress_reports.admitted_trainee_disabled',
            'training_center_progress_reports.admitted_trainee_qawmi',
            'training_center_progress_reports.admitted_trainee_transgender',
            'training_center_progress_reports.admitted_trainee_others',
            'training_center_progress_reports.admitted_trainee_total',

            'training_center_progress_reports.technical_board_registered_trainee_men',
            'training_center_progress_reports.technical_board_registered_trainee_women',
            'training_center_progress_reports.technical_board_registered_trainee_disabled',
            'training_center_progress_reports.technical_board_registered_trainee_qawmi',
            'training_center_progress_reports.technical_board_registered_trainee_transgender',
            'training_center_progress_reports.technical_board_registered_trainee_others',
            'training_center_progress_reports.technical_board_registered_trainee_total',


            'training_center_progress_reports.latest_test_attended_trainee_men',
            'training_center_progress_reports.latest_test_attended_trainee_women',
            'training_center_progress_reports.latest_test_attended_trainee_disabled',
            'training_center_progress_reports.latest_test_attended_trainee_qawmi',
            'training_center_progress_reports.latest_test_attended_trainee_transgender',
            'training_center_progress_reports.latest_test_attended_trainee_others',
            'training_center_progress_reports.latest_test_attended_trainee_total',

            'training_center_progress_reports.latest_test_passed_trainee_men',
            'training_center_progress_reports.latest_test_passed_trainee_women',
            'training_center_progress_reports.latest_test_passed_trainee_disabled',
            'training_center_progress_reports.latest_test_passed_trainee_qawmi',
            'training_center_progress_reports.latest_test_passed_trainee_transgender',
            'training_center_progress_reports.latest_test_passed_trainee_others',
            'training_center_progress_reports.latest_test_passed_trainee_total',

            'training_center_progress_reports.created_at',
            'training_center_progress_reports.updated_at',

        ])->acl();

        $trainingCenterProgressReportBuilder->join("institutes", function ($join) {
            $join->on('training_center_progress_reports.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterProgressReportBuilder->join("training_centers", function ($join) {
            $join->on('training_center_progress_reports.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $trainingCenterProgressReportBuilder->orderBy('training_center_progress_reports.id', $order);

        if (is_numeric($instituteId)) {
            $trainingCenterProgressReportBuilder->where('training_center_progress_reports.institute_id', '=', $instituteId);
        }
        if (is_numeric($trainingCenterId)) {
            $trainingCenterProgressReportBuilder->where('training_center_progress_reports.training_center_id', '=', $trainingCenterId);
        }

        /** @var Collection $trainingCenterProgressReports */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainingCenterProgressReports = $trainingCenterProgressReportBuilder->paginate($pageSize);
            $paginateData = (object)$trainingCenterProgressReports->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenterProgressReports = $trainingCenterProgressReportBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainingCenterProgressReports->toArray()['data'] ?? $trainingCenterProgressReports->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;

    }

    /**
     * @param array $data
     * @return TrainingCenterProgressReport
     */
    public function store(array $data): TrainingCenterProgressReport
    {
        $data['admitted_trainee_total'] = $data['admitted_trainee_men'] + $data['admitted_trainee_women'] + $data['admitted_trainee_disabled'] + $data['admitted_trainee_qawmi'] + $data['admitted_trainee_transgender'] + $data['admitted_trainee_others'];
        $data['technical_board_registered_trainee_total'] = $data['technical_board_registered_trainee_men'] + $data['technical_board_registered_trainee_women'] + $data['technical_board_registered_trainee_disabled'] + $data['technical_board_registered_trainee_qawmi'] + $data['technical_board_registered_trainee_transgender'] + $data['technical_board_registered_trainee_others'];
        $data['latest_test_attended_trainee_total'] = $data['latest_test_attended_trainee_men'] + $data['latest_test_attended_trainee_women'] + $data['latest_test_attended_trainee_disabled'] + $data['latest_test_attended_trainee_qawmi'] + $data['latest_test_attended_trainee_transgender'] + $data['latest_test_attended_trainee_others'];
        $data['latest_test_passed_trainee_total'] = $data['latest_test_passed_trainee_men'] + $data['latest_test_passed_trainee_women'] + $data['latest_test_passed_trainee_disabled'] + $data['latest_test_passed_trainee_qawmi'] + $data['latest_test_passed_trainee_transgender'] + $data['latest_test_passed_trainee_others'];
        $trainingCenterProgressReport = app(TrainingCenterProgressReport::class);
        $trainingCenterProgressReport->fill($data);
        $trainingCenterProgressReport->save();

        return $trainingCenterProgressReport;
    }


    /**
     * @param int $id
     * @return Model|Builder
     */
    public function getOneTrainingCenterProgressReport(int $id): Model|Builder
    {

        /** @var Builder|TrainingCenterProgressReport $trainingCenterProgressReportBuilder */
        $trainingCenterProgressReportBuilder = TrainingCenterProgressReport::select([
            'training_center_progress_reports.id',
            'training_center_progress_reports.institute_id',
            'training_center_progress_reports.training_center_id',
            'training_center_progress_reports.reporting_month',
            'training_center_progress_reports.trade_name',
            'training_center_progress_reports.number_of_trainers',
            'training_center_progress_reports.number_of_labs_or_training_rooms',
            'training_center_progress_reports.number_of_computers_or_training_equipments',

            'training_center_progress_reports.admitted_trainee_men',
            'training_center_progress_reports.admitted_trainee_women',
            'training_center_progress_reports.admitted_trainee_disabled',
            'training_center_progress_reports.admitted_trainee_qawmi',
            'training_center_progress_reports.admitted_trainee_transgender',
            'training_center_progress_reports.admitted_trainee_others',
            'training_center_progress_reports.admitted_trainee_total',

            'training_center_progress_reports.technical_board_registered_trainee_men',
            'training_center_progress_reports.technical_board_registered_trainee_women',
            'training_center_progress_reports.technical_board_registered_trainee_disabled',
            'training_center_progress_reports.technical_board_registered_trainee_qawmi',
            'training_center_progress_reports.technical_board_registered_trainee_transgender',
            'training_center_progress_reports.technical_board_registered_trainee_others',
            'training_center_progress_reports.technical_board_registered_trainee_total',


            'training_center_progress_reports.latest_test_attended_trainee_men',
            'training_center_progress_reports.latest_test_attended_trainee_women',
            'training_center_progress_reports.latest_test_attended_trainee_disabled',
            'training_center_progress_reports.latest_test_attended_trainee_qawmi',
            'training_center_progress_reports.latest_test_attended_trainee_transgender',
            'training_center_progress_reports.latest_test_attended_trainee_others',
            'training_center_progress_reports.latest_test_attended_trainee_total',

            'training_center_progress_reports.latest_test_passed_trainee_men',
            'training_center_progress_reports.latest_test_passed_trainee_women',
            'training_center_progress_reports.latest_test_passed_trainee_disabled',
            'training_center_progress_reports.latest_test_passed_trainee_qawmi',
            'training_center_progress_reports.latest_test_passed_trainee_transgender',
            'training_center_progress_reports.latest_test_passed_trainee_others',
            'training_center_progress_reports.latest_test_passed_trainee_total',

            'training_center_progress_reports.created_at',
            'training_center_progress_reports.updated_at',

        ]);

        $trainingCenterProgressReportBuilder->where('training_center_progress_reports.id', $id);

        return $trainingCenterProgressReportBuilder->firstOrFail();

    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id,deleted_at,NULL',
            ],
            'training_center_id' => [
                'required',
                'int',
                'exists:training_centers,id,deleted_at,NULL',
            ],
            'reporting_month' => [
                'required',
                'date',
            ],
            'trade_name' => 'nullable|string',
            'number_of_trainers' => 'nullable|int|min:1',
            'number_of_labs_or_training_rooms' => 'nullable|int|min:1',
            'number_of_computers_or_training_equipments' => 'nullable|int|min:1',

            'admitted_trainee_men' => 'nullable|int|min:1',
            'admitted_trainee_women' => 'nullable|int|min:1',
            'admitted_trainee_disable' => 'nullable|int|min:1',
            'admitted_trainee_qawmi' => 'nullable|int|min:1',
            'admitted_trainee_transgender' => 'nullable|int|min:1',
            'admitted_trainee_others' => 'nullable|int|min:1',

            'technical_board_registered_trainee_men' => 'nullable|int|min:1',
            'technical_board_registered_trainee_women' => 'nullable|int|min:1',
            'technical_board_registered_trainee_disabled' => 'nullable|int|min:1',
            'technical_board_registered_trainee_qawmi' => 'nullable|int|min:1',
            'technical_board_registered_trainee_transgender' => 'nullable|int|min:1',
            'technical_board_registered_trainee_others' => 'nullable|int|min:1',


            'latest_test_attended_trainee_men' => 'nullable|int|min:1',
            'latest_test_attended_trainee_women' => 'nullable|int|min:1',
            'latest_test_attended_trainee_disabled' => 'nullable|int|min:1',
            'latest_test_attended_trainee_qawmi' => 'nullable|int|min:1',
            'latest_test_attended_trainee_transgender' => 'nullable|int|min:1',
            'latest_test_attended_trainee_others' => 'nullable|int|min:1',

            'latest_test_passed_trainee_men' => 'nullable|int|min:1',
            'latest_test_passed_trainee_women' => 'nullable|int|min:1',
            'latest_test_passed_trainee_disabled' => 'nullable|int|min:1',
            'latest_test_passed_trainee_qawmi' => 'nullable|int|min:1',
            'latest_test_passed_trainee_transgender' => 'nullable|int|min:1',
            'latest_test_passed_trainee_others' => 'nullable|int|min:1',

        ];

        return Validator::make($request->all(), $rules);
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
            'institute_id' => 'nullable|int|gt:0',
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
