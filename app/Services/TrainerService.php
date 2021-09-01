<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

//use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrainerService
 * @package App\Services
 */
class TrainerService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainerList(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $instituteId = array_key_exists('institute_id', $request) ? $request['institute_id'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::select([
            'trainers.id',
            'trainers.trainer_name_en',
            'trainers.trainer_name_bn',
            'trainers.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'trainers.training_center_id',
            'training_centers.title_en as training_centers_title_en',
            'training_centers.title_bn as training_centers_title_bn',
            'trainers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title_bn as branch_title_bn',
            'trainers.email',
            'trainers.date_of_birth as date_of_birth',
            'trainers.about_me',
            'trainers.gender',
            'trainers.marital_status',
            'trainers.religion',
            'trainers.nationality',
            'trainers.nid',
            'trainers.passport_number',
            'trainers.physical_disabilities_status',
            'trainers.freedom_fighter_status',
            'trainers.present_address_division_id',
            'loc_divisions.title_bn as present_address_division_title_bn',
            'loc_divisions.title_en as present_address_division_title_en',
            'trainers.present_address_district_id',
            'loc_districts.title_bn as present_address_district_title_bn',
            'loc_districts.title_en as present_address_district_title_en',
            'trainers.present_address_upazila_id',
            'loc_upazilas.title_bn as present_address_upazila_title_bn',
            'loc_upazilas.title_en as present_address_upazila_title_en',
            'trainers.present_house_address',
            'trainers.permanent_address_division_id',
            'trainers.permanent_address_district_id',
            'trainers.permanent_address_upazila_id',
            'trainers.permanent_house_address',
            'trainers.educational_qualification',
            'trainers.skills',
            'trainers.photo',
            'trainers.signature',
            'trainers.row_status',
            'trainers.created_at',
            'trainers.updated_at',
        ]);

        $trainerBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('trainers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftjoin("training_centers", function ($join) use ($rowStatus) {
            $join->on('trainers.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('training_centers.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftjoin("branches", function ($join) use ($rowStatus) {
            $join->on('trainers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftJoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('loc_divisions.id', '=', 'trainers.present_address_division_id')
                ->whereNull('loc_divisions.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_divisions.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftJoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('loc_districts.id', '=', 'trainers.present_address_district_id')
                ->whereNull('loc_districts.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_districts.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftJoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('loc_upazilas.id', '=', 'trainers.present_address_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_upazilas.row_status', $rowStatus);
            }
        });

        $trainerBuilder->orderBy('trainers.id', $order);

        if (is_numeric($rowStatus)) {
            $trainerBuilder->where('trainers.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $trainerBuilder->where('trainers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainerBuilder->where('trainers.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($instituteId) {
            $trainerBuilder->where('trainers.institute_id', '=', $instituteId);
        }

        /** @var Collection $trainerBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainers = $trainerBuilder->paginate($pageSize);
            $paginateData = (object)$trainers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainers = $trainerBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainers->toArray()['data'] ?? $trainers->toArray();
        $response['response_status'] = [
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
    public function getOneTrainer(int $id, Carbon $startTime): array
    {
        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::select([
            'trainers.id',
            'trainers.trainer_name_en',
            'trainers.trainer_name_bn',
            'trainers.institute_id',
            'institutes.title_en as institutes_title_en',
            'institutes.title_bn as institutes_title_bn',
            'trainers.training_center_id',
            'training_centers.title_en as training_centers_title_en',
            'training_centers.title_bn as training_centers_title_bn',
            'trainers.branch_id',
            'branches.title_en as branches_title_en',
            'branches.title_bn as branches_title_bn',
            'trainers.email',
            'trainers.date_of_birth as date_of_birth',
            'trainers.about_me',
            'trainers.gender',
            'trainers.marital_status',
            'trainers.religion',
            'trainers.nationality',
            'trainers.nid',
            'trainers.passport_number',
            'trainers.physical_disabilities_status',
            'trainers.freedom_fighter_status',
            'trainers.present_address_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'trainers.present_address_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'trainers.present_address_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'trainers.present_house_address',
            'trainers.permanent_address_division_id',
            'trainers.permanent_address_district_id',
            'trainers.permanent_address_upazila_id',
            'trainers.permanent_house_address',
            'trainers.educational_qualification',
            'trainers.skills',
            'trainers.photo',
            'trainers.signature',
            'trainers.row_status',
            'trainers.created_at',
            'trainers.updated_at',
        ]);

        $trainerBuilder->join("institutes", function ($join) {
            $join->on('trainers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $trainerBuilder->leftJoin("training_centers", function ($join) {
            $join->on('trainers.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $trainerBuilder->leftjoin("branches", function ($join) {
            $join->on('trainers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'trainers.present_address_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'trainers.present_address_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'trainers.present_address_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $trainerBuilder->where('trainers.id', $id);

        /** @var Trainer $trainerBuilder */
        $trainer = $trainerBuilder->first();
        return [
            "data" => $trainer ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param array $data
     * @return Trainer
     */
    public function store(array $data): Trainer
    {
        $trainer = new Trainer();
        $trainer->fill($data);
        $trainer->Save();
        return $trainer;
    }

    /**
     * @param Trainer $trainer
     * @param array $data
     * @return Trainer
     */
    public function update(Trainer $trainer, array $data): Trainer
    {
        $trainer->fill($data);
        $trainer->save();
        return $trainer;
    }

    /**
     * @param Trainer $trainer
     * @return bool
     */
    public function destroy(Trainer $trainer): bool
    {
        return $trainer->delete();
    }

    /**
     * @param Trainer $trainer
     * @param array $batchIds
     * @return Trainer
     */
    public function assignTrainer(Trainer $trainer, array $batchIds): Trainer
    {
        $validTrainers = Batch::whereIn('id', $batchIds)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $trainer->batches()->syncWithoutDetaching($validTrainers);
        return $trainer;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $rules = [
            'trainer_name_en' => [
                'required',
                'string',
                'max:191'
            ],
            'trainer_name_bn' => [
                'required',
                'string',
                'max:1000'
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id'
            ],
            'branch_id' => [
                'nullable',
                'int',
                'exists:branches,id'
            ],
            'training_center_id' => [
                'nullable',
                'int',
                'exists:training_centers,id'
            ],
            'trainer_registration_number' => [
                'nullable',
                'string',
                'unique:trainers,trainer_registration_number,' . $id
            ],
            'email' => [
                'nullable',
                'string',
                'unique:trainers,email,' . $id
            ],
            'mobile' => [
                'nullable',
                'string',
                'unique:trainers,mobile,' . $id
            ],
            'date_of_birth' => [
                'nullable',
                'date-time'
            ],
            'about_me' => [
                'nullable',
                'string'
            ],
            'gender' => [
                'nullable',
                'int'
            ],
            'marital_status' => [
                'nullable',
                'int'
            ],
            'religion' => [
                'nullable',
                'int'
            ],
            'nationality' => [
                'nullable',
                'string'
            ],
            'nid' => [
                'nullable',
                'string'
            ],
            'passport_number' => [
                'nullable',
                'string'
            ],
            'physical_disabilities_status' => [
                'nullable',
                'int'
            ],
            'freedom_fighter_status' => [
                'nullable',
                'int'
            ],
            'present_address_division_id' => [
                'nullable',
                'int'
            ],
            'present_address_district_id' => [
                'nullable',
                'int'
            ],
            'present_address_upazila_id' => [
                'nullable',
                'int'
            ],
            'present_house_address' => [
                'nullable',
                'string'
            ],
            'permanent_address_division_id' => [
                'nullable',
                'int'
            ],
            'permanent_address_district_id' => [
                'nullable',
                'int'
            ],
            'permanent_address_upazila_id' => [
                'nullable',
                'int'
            ],
            'permanent_house_address' => [
                'nullable',
                'string'
            ],
            'educational_qualification' => [
                'nullable',
                'string'
            ],
            'skills' => [
                'nullable',
                'string'
            ],
            'photo' => [
                'nullable',
                'string'
            ],
            'signature' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    public function batchValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data["batchIds"] = is_array($request['batchIds']) ? $request['batchIds'] : explode(',', $request['batchIds']);

        $rules = [
            'batchIds' => 'required|array|min:1',
            'batchIds.*' => 'required|integer|distinct|min:1'
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }

    public function getTrainerTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::onlyTrashed()->select([
            'trainers.id as id',
            'trainers.trainer_name_en',
            'trainers.trainer_name_bn',
            'trainers.institute_id',
            'trainers.training_center_id',
            'trainers.branch_id',
            'trainers.email',
            'trainers.date_of_birth as date_of_birth',
            'trainers.about_me',
            'trainers.gender as gender',
            'trainers.marital_status as marital_status',
            'trainers.religion as religion',
            'trainers.nationality as nationality',
            'trainers.nid',
            'trainers.passport_number as passport_number',
            'trainers.physical_disabilities_status as physical_disabilities_status',
            'trainers.freedom_fighter_status as freedom_fighter_status',
            'trainers.present_address_division_id as present_address_division_id',
            'trainers.present_address_district_id as present_address_district_id',
            'trainers.present_address_upazila_id as present_address_upazila_id',
            'trainers.present_house_address as present_house_address',
            'trainers.permanent_address_division_id as permanent_address_division_id',
            'trainers.permanent_address_district_id as permanent_address_district_id',
            'trainers.permanent_address_upazila_id as permanent_address_upazila_id',
            'trainers.permanent_house_address as permanent_house_address',
            'trainers.educational_qualification as educational_qualification',
            'trainers.skills as skills',
            'trainers.photo as photo',
            'trainers.signature as signature',
            'trainers.row_status',
            'trainers.created_at',
            'trainers.updated_at',
        ]);

        $trainerBuilder->orderBy('trainers.id', $order);

        if (!empty($titleEn)) {
            $trainerBuilder->where('trainers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainerBuilder->where('trainers.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $trainerBuilder */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $trainers = $trainerBuilder->paginate($limit);
            $paginateData = (object)$trainers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainers = $trainerBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainers->toArray()['data'] ?? $trainers->toArray();
        $response['response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    public function restore(Trainer $trainer): bool
    {
        return $trainer->restore();
    }

    public function forceDelete(Trainer $trainer): bool
    {
        return $trainer->forceDelete();
    }

    public function filterValidator(Request $request): Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC',
            'row_status.in' => 'Row status must be within 1 or 0'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page_size' => 'numeric',
            'page' => 'numeric',
            'institute_id' => 'numeric',
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
}
