<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Trainer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BatchService
 * @package App\Services
 */
class BatchService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getBatchList(array $request, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $branchId = $request['branch_id'] ?? "";
        $programId = $request['programme_id'] ?? "";
        $courseId = $request['course_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var Batch|Builder $batchBuilder */
        $batchBuilder = Batch::select([
            'batches.id',
            'batches.course_id',
            'courses.title_en as course_title_en',
            'courses.title_bn as course_title_bn',
            'batches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'batches.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title_bn as branch_title_bn',
            'batches.programme_id',
            'programmes.title_en as programme_title_en',
            'programmes.title_bn as programme_title_bn',
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
            'batches.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title_bn as training_center_title_bn',
            'batches.dynamic_form_field',
            'batches.row_status',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at',
            'batches.deleted_at',
        ]);

        $batchBuilder->join("courses", function ($join) use ($rowStatus) {
            $join->on('batches.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('courses.row_status', $rowStatus);
            }
        });
        $batchBuilder->leftjoin("institutes", function ($join) use ($rowStatus) {
            $join->on('batches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $batchBuilder->leftjoin("programmes", function ($join) use ($rowStatus) {
            $join->on('batches.programme_id', '=', 'programmes.id')
                ->whereNull('programmes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('programmes.row_status', $rowStatus);
            }
        });

        $batchBuilder->leftjoin("branches", function ($join) use ($rowStatus) {
            $join->on('batches.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }
        });

        $batchBuilder->join("training_centers", function ($join) use ($rowStatus) {
            $join->on('batches.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('training_centers.row_status', $rowStatus);
            }
        });

        $batchBuilder->orderBy('batches.id', $order);

        if (is_numeric($rowStatus)) {
            $batchBuilder->where('batches.row_status', $rowStatus);
        }

        if (is_numeric($instituteId)) {
            $batchBuilder->where('batches.institute_id', $instituteId);
        }

        if (is_numeric($branchId)) {
            $batchBuilder->where('batches.branch_id', $branchId);
        }

        if (is_numeric($programId)) {
            $batchBuilder->where('batches.programme_id', $programId);
        }

        if (is_numeric($courseId)) {
            $batchBuilder->where('batches.course_id', $courseId);
        }

        if (is_numeric($trainingCenterId)) {
            $batchBuilder->where('batches.training_center_id', $trainingCenterId);
        }

        /** @var Collection $batches */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $batches = $batchBuilder->paginate($pageSize);
            $paginateData = (object)$batches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $batches = $batchBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $batches->toArray()['data'] ?? $batches->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getBatch(int $id, Carbon $startTime): array
    {
        /** @var Batch|Builder $batchBuilder */

        $batchBuilder = Batch::select([
            'batches.id',
            'batches.course_id',
            'courses.title_en as course_title_en',
            'courses.title_bn as course_title_bn',
            'batches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'batches.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title_bn as branch_title_bn',
            'batches.programme_id',
            'programmes.title_en as programme_title_en',
            'programmes.title_bn as programme_title_bn',
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
            'batches.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title_bn as training_center_title_bn',
            'batches.dynamic_form_field',
            'batches.row_status',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at',
            'batches.deleted_at',
        ]);

        $batchBuilder->join("courses", function ($join) {
            $join->on('batches.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
        });
        $batchBuilder->leftjoin("institutes", function ($join) {
            $join->on('batches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $batchBuilder->leftjoin("programmes", function ($join) {
            $join->on('batches.programme_id', '=', 'programmes.id')
                ->whereNull('programmes.deleted_at');
        });

        $batchBuilder->leftjoin("branches", function ($join) {
            $join->on('batches.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });
        $batchBuilder->join("training_centers", function ($join) {
            $join->on('batches.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $batchBuilder->where('batches.id', $id);

        $batchBuilder->with('trainers');

        /** @var Batch $batch */
        $batch = $batchBuilder->first();

        return [
            "data" => $batch ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];

    }


    /**
     * @param array $data
     * @return Batch
     */
    public function store(array $data): Batch
    {
        $courseConfig = new Batch();
        $courseConfig->fill($data);
        $courseConfig->save();
        return $courseConfig;
    }

    /**
     * @param Batch $courseConfig
     * @param array $data
     * @return Batch
     */
    public function update(Batch $courseConfig, array $data): Batch
    {
        $courseConfig->fill($data);
        $courseConfig->save();
        return $courseConfig;

    }

    /**
     * @param Batch $batch
     * @return bool
     */
    public function destroy(Batch $batch): bool
    {
        return $batch->delete();
    }


    public function getBatchTrashList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $limit = $request->query('limit', 10);
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Batch|Builder $batchBuilder */
        $batchBuilder = Batch::onlyTrashed()->select([
            'batches.id as id',
            'batches.course_id',
            'courses.title_en as course_title',
            'batches.institute_id',
            'institutes.title_en as institute_title',
            'institutes.id as institute_id',
            'branches.id as branch_id',
            'branches.title_en as branch_name',
            'batches.programme_id',
            'programmes.title_en as programme_name',
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
            'training_centers.id as training_center_id',
            'training_centers.title_en as training_center_name',
            'batches.in_ethnic_group',
            'batches.is_freedom_fighter',
            'batches.disability_status',
            'batches.ssc_passing_status',
            'batches.hsc_passing_status',
            'batches.honors_passing_status',
            'batches.masters_passing_status',
            'batches.is_occupation_needed',
            'batches.is_guardian_info_needed',
            'batches.row_status',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at'
        ]);

        $batchBuilder->join('courses', 'batches.course_id', '=', 'courses.id');
        $batchBuilder->join('institutes', 'batches.institute_id', '=', 'institutes.id');
        $batchBuilder->leftJoin('programmes', 'batches.programme_id', '=', 'programmes.id');
        $batchBuilder->leftJoin('branches', 'batches.branch_id', '=', 'branches.id');
        $batchBuilder->leftJoin('training_centers', 'batches.training_center_id', '=', 'training_centers.id');

        $batchBuilder->orderBy('batches.id', $order);


        if (!empty($titleEn)) {
            $batchBuilder->where('batches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $batchBuilder->where('batches.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $batches */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $batches = $batchBuilder->paginate($limit);
            $paginateData = (object)$batches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $batches = $batchBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $batches->toArray()['data'] ?? $batches->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param Batch $batch
     * @param array $trainerIds
     * @return Batch
     */
    public function assignTrainer(Batch $batch, array $trainerIds): Batch
    {
        $validTrainers = Trainer::whereIn('id', $trainerIds)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $batch->trainers()->sync($validTrainers);
        return $batch;
    }

    public function restore(Batch $batch): bool
    {
        return $batch->restore();
    }

    public function forceDelete(Batch $batch): bool
    {
        return $batch->forceDelete();
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = Null): \Illuminate\Contracts\Validation\Validator
    {

        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        $rules = [
            'institute_id' => [
                'nullable',
                'int',
                'exists:institutes,id',
            ],
            'course_id' => [
                'required',
                'int',
                'exists:courses,id'
            ],
            'training_center_id' => [
                'required',
                'int',
                'exists:training_centers,id'
            ],
            'programme_id' => [
                'nullable',
                'int',
                'exists:programmes,id'
            ],
            'branch_id' => [
                'nullable',
                'int',
                'exists:branches,id'
            ],
            'number_of_seats' => [
                'required',
                'int'
            ],
            'registration_start_date' => [
                'required',
            ],
            'registration_end_date' => [
                'required',
                'after:registration_start_date',
                'date_format:Y-m-d',
            ],
            'batch_start_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'batch_end_date' => [
                'required',
                'date_format:Y-m-d',
                'after:batch_start_date'
            ],
            'available_seats' => [
                'int',
                'nullable'
            ],
            "dynamic_form_field" => [
                'nullable',
                "string"
            ],
            'loc_district_id' => [
                'nullable',
                'exists:loc_districts,id'
            ],
            'loc_division_id' => [
                'nullable',
                'exists:loc_divisions,id'
            ],
            'loc_upazila_id' => [
                'nullable',
                'exists:loc_upazilas,id'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => [
                'nullable',
                'integer',
                'max:10'
            ],
            'updated_by' => [
                'nullable',
                'integer',
                'max:10'
            ],
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }

        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        return Validator::make($request->all(), [
            'page_size' => 'numeric|gt:0',
            'page' => 'numeric|gt:0',
            'institute_id' => 'numeric|exists:institutes,id',
            'branch_id' => 'numeric|exists:branches,id',
            'programme_id' => 'numeric|exists:programmes,id',
            'course_id' => 'numeric|exists:courses,id',
            'training_center_id' => 'numeric|exists:training_centers,id',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function trainerValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data["trainerIds"] = is_array($request['trainerIds']) ? $request['trainerIds'] : explode(',', $request['trainerIds']);

        $rules = [
            'trainerIds' => 'required|array|min:1',
            'trainerIds.*' => 'required|integer|distinct|min:1'
        ];
        return Validator::make($data, $rules);
    }


}

