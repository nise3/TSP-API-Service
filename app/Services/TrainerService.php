<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
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
        $nameEn = $request['trainer_name_en'] ?? "";
        $name = $request['trainer_name'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $branchId = $request['branch_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::select([
            'trainers.id',
            'trainers.institute_id',
            'institutes.title_en as institutes_title_en',
            'institutes.title as institutes_title',
            'trainers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'trainers.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            'trainers.trainer_name',
            'trainers.trainer_name_en',
            'trainers.trainer_registration_number',
            'trainers.email',
            'trainers.mobile',
            'trainers.date_of_birth',
            'trainers.about_me',
            'trainers.about_me_en',
            'trainers.educational_qualification',
            'trainers.educational_qualification_en',
            'trainers.skills',
            'trainers.skills_en',
            'trainers.gender',
            'trainers.marital_status',
            'trainers.religion',
            'trainers.nationality',
            'trainers.nid',
            'trainers.passport_number',
            'trainers.present_address_division_id',
            'loc_divisions_present.title as division_title_present_address',
            'loc_divisions_present.title_en as division_title_en_present_address',
            'trainers.present_address_district_id',
            'loc_districts_present.title as district_title_present_address',
            'loc_districts_present.title_en as district_title_en_present_address',
            'trainers.present_address_upazila_id',
            'loc_upazilas_present.title as upazila_title_present_address',
            'loc_upazilas_present.title_en as upazila_title_en_present_address',
            'trainers.present_house_address',
            'trainers.present_house_address_en',
            'trainers.permanent_address_division_id',
            'loc_divisions_permanent.title as division_title_permanent_address',
            'loc_divisions_permanent.title_en as division_title_en_permanent_address',
            'trainers.permanent_address_district_id',
            'loc_districts_permanent.title as district_title_permanent_address',
            'loc_districts_permanent.title_en as district_title_en_present_address',
            'trainers.permanent_address_upazila_id',
            'loc_upazilas_permanent.title as upazila_title_permanent_address',
            'loc_upazilas_permanent.title_en as upazila_title_en_permanent_address',
            'trainers.permanent_house_address',
            'trainers.permanent_house_address_en',
            'trainers.photo',
            'trainers.signature',
            'trainers.row_status',
            'trainers.created_by',
            'trainers.updated_by',
            'trainers.created_at',
            'trainers.updated_at',
            'trainers.deleted_at',
        ]);

        $trainerBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('trainers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });
        $trainerBuilder->leftJoin("training_centers", function ($join) use ($rowStatus) {
            $join->on('trainers.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('training_centers.row_status', $rowStatus);
            }
        });
        $trainerBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('trainers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftJoin('loc_divisions as loc_divisions_present', function ($join) {
            $join->on('loc_divisions_present.id', '=', 'trainers.present_address_division_id')
                ->whereNull('loc_divisions_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts as loc_districts_present', function ($join) {
            $join->on('loc_districts_present.id', '=', 'trainers.present_address_district_id')
                ->whereNull('loc_districts_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_upazilas as loc_upazilas_present', function ($join) {
            $join->on('loc_upazilas_present.id', '=', 'trainers.present_address_upazila_id')
                ->whereNull('loc_upazilas_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_divisions as loc_divisions_permanent', function ($join) {
            $join->on('loc_divisions_permanent.id', '=', 'trainers.permanent_address_division_id')
                ->whereNull('loc_divisions_permanent.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts as loc_districts_permanent', function ($join) use ($rowStatus) {
            $join->on('loc_districts_permanent.id', '=', 'trainers.permanent_address_district_id')
                ->whereNull('loc_districts_permanent.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('loc_districts_permanent.row_status', $rowStatus);
            }
        });

        $trainerBuilder->leftJoin('loc_upazilas as loc_upazilas_permanent', function ($join) use ($rowStatus) {
            $join->on('loc_upazilas_permanent.id', '=', 'trainers.permanent_address_upazila_id')
                ->whereNull('loc_upazilas_permanent.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('loc_upazilas_permanent.row_status', $rowStatus);
            }
        });

        $trainerBuilder->orderBy('trainers.id', $order);

        if (is_int($rowStatus)) {
            $trainerBuilder->where('trainers.row_status', $rowStatus);
        }

        if (!empty($nameEn)) {
            $trainerBuilder->where('trainers.trainer_name_en', 'like', '%' . $nameEn . '%');
        }
        if (!empty($name)) {
            $trainerBuilder->where('trainers.trainer_name', 'like', '%' . $name . '%');
        }

        if (is_int($instituteId)) {
            $trainerBuilder->where('trainers.institute_id', '=', $instituteId);
        }

        if (is_int($branchId)) {
            $trainerBuilder->where('trainers.branch_id', '=', $branchId);
        }

        if (is_int($trainingCenterId)) {
            $trainerBuilder->where('trainers.training_center_id', '=', $trainingCenterId);
        }

        /** @var Collection $trainers */
        if (is_int($paginate) || is_int($pageSize)) {
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
    public function getOneTrainer(int $id, Carbon $startTime): array
    {
        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::select([
            'trainers.id',
            'trainers.institute_id',
            'institutes.title_en as institutes_title_en',
            'institutes.title as institutes_title',
            'trainers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'trainers.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            'trainers.trainer_name',
            'trainers.trainer_name_en',
            'trainers.trainer_registration_number',
            'trainers.email',
            'trainers.mobile',
            'trainers.date_of_birth',
            'trainers.about_me',
            'trainers.about_me_en',
            'trainers.educational_qualification',
            'trainers.educational_qualification_en',
            'trainers.skills',
            'trainers.skills_en',
            'trainers.gender',
            'trainers.marital_status',
            'trainers.religion',
            'trainers.nationality',
            'trainers.nid',
            'trainers.passport_number',
            'trainers.present_address_division_id',
            'loc_divisions_present.title as division_title_present_address',
            'loc_divisions_present.title_en as division_title_en_present_address',
            'trainers.present_address_district_id',
            'loc_districts_present.title as district_title_present_address',
            'loc_districts_present.title_en as district_title_en_present_address',
            'trainers.present_address_upazila_id',
            'loc_upazilas_present.title as upazila_title_present_address',
            'loc_upazilas_present.title_en as upazila_title_en_present_address',
            'trainers.present_house_address',
            'trainers.present_house_address_en',
            'trainers.permanent_address_division_id',
            'loc_divisions_permanent.title as division_title_permanent_address',
            'loc_divisions_permanent.title_en as division_title_en_permanent_address',
            'trainers.permanent_address_district_id',
            'loc_districts_permanent.title as district_title_permanent_address',
            'loc_districts_permanent.title_en as district_title_en_present_address',
            'trainers.permanent_address_upazila_id',
            'loc_upazilas_permanent.title as upazila_title_permanent_address',
            'loc_upazilas_permanent.title_en as upazila_title_en_permanent_address',
            'trainers.permanent_house_address',
            'trainers.permanent_house_address_en',
            'trainers.photo',
            'trainers.signature',
            'trainers.row_status',
            'trainers.created_by',
            'trainers.updated_by',
            'trainers.created_at',
            'trainers.updated_at',
            'trainers.deleted_at',
        ]);

        $trainerBuilder->join("institutes", function ($join) {
            $join->on('trainers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainerBuilder->leftJoin("training_centers", function ($join) {
            $join->on('trainers.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });
        $trainerBuilder->leftJoin("branches", function ($join) {
            $join->on('trainers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_divisions as loc_divisions_present', function ($join) {
            $join->on('loc_divisions_present.id', '=', 'trainers.present_address_division_id')
                ->whereNull('loc_divisions_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts as loc_districts_present', function ($join) {
            $join->on('loc_districts_present.id', '=', 'trainers.present_address_district_id')
                ->whereNull('loc_districts_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_upazilas as loc_upazilas_present', function ($join) {
            $join->on('loc_upazilas_present.id', '=', 'trainers.present_address_upazila_id')
                ->whereNull('loc_upazilas_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_divisions as loc_divisions_permanent', function ($join) {
            $join->on('loc_divisions_permanent.id', '=', 'trainers.permanent_address_division_id')
                ->whereNull('loc_divisions_permanent.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts as loc_districts_permanent', function ($join) {
            $join->on('loc_districts_permanent.id', '=', 'trainers.permanent_address_district_id')
                ->whereNull('loc_districts_permanent.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_upazilas as loc_upazilas_permanent', function ($join) {
            $join->on('loc_upazilas_permanent.id', '=', 'trainers.permanent_address_upazila_id')
                ->whereNull('loc_upazilas_permanent.deleted_at');
        });

        $trainerBuilder->where('trainers.id', $id);

        /** @var Trainer $trainer */
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

    public function getTrainerTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::onlyTrashed()->select([
            'trainers.id',
            'trainers.institute_id',
            'institutes.title_en as institutes_title_en',
            'institutes.title as institutes_title',
            'trainers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'trainers.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            'trainers.trainer_name',
            'trainers.trainer_name_en',
            'trainers.trainer_registration_number',
            'trainers.email',
            'trainers.mobile',
            'trainers.date_of_birth',
            'trainers.about_me',
            'trainers.about_me_en',
            'trainers.educational_qualification',
            'trainers.educational_qualification_en',
            'trainers.skills',
            'trainers.skills_en',
            'trainers.gender',
            'trainers.marital_status',
            'trainers.religion',
            'trainers.nationality',
            'trainers.nid',
            'trainers.passport_number',
            'trainers.present_address_division_id',
            'loc_divisions_present.title as division_title_present_address',
            'loc_divisions_present.title_en as division_title_en_present_address',
            'trainers.present_address_district_id',
            'loc_districts_present.title as district_title_present_address',
            'loc_districts_present.title_en as district_title_en_present_address',
            'trainers.present_address_upazila_id',
            'loc_upazilas_present.title as upazila_title_present_address',
            'loc_upazilas_present.title_en as upazila_title_en_present_address',
            'trainers.present_house_address',
            'trainers.present_house_address_en',
            'trainers.permanent_address_division_id',
            'loc_divisions_permanent.title as division_title_permanent_address',
            'loc_divisions_permanent.title_en as division_title_en_permanent_address',
            'trainers.permanent_address_district_id',
            'loc_districts_permanent.title as district_title_permanent_address',
            'loc_districts_permanent.title_en as district_title_en_present_address',
            'trainers.permanent_address_upazila_id',
            'loc_upazilas_permanent.title as upazila_title_permanent_address',
            'loc_upazilas_permanent.title_en as upazila_title_en_permanent_address',
            'trainers.permanent_house_address',
            'trainers.permanent_house_address_en',
            'trainers.photo',
            'trainers.signature',
            'trainers.row_status',
            'trainers.created_by',
            'trainers.updated_by',
            'trainers.created_at',
            'trainers.updated_at',
            'trainers.deleted_at',
        ]);

        $trainerBuilder->join("institutes", function ($join) {
            $join->on('trainers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainerBuilder->leftJoin("training_centers", function ($join) {
            $join->on('trainers.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });
        $trainerBuilder->leftJoin("branches", function ($join) {
            $join->on('trainers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_divisions as loc_divisions_present', function ($join) {
            $join->on('loc_divisions_present.id', '=', 'trainers.present_address_division_id')
                ->whereNull('loc_divisions_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts as loc_districts_present', function ($join) {
            $join->on('loc_districts_present.id', '=', 'trainers.present_address_district_id')
                ->whereNull('loc_districts_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_upazilas as loc_upazilas_present', function ($join) {
            $join->on('loc_upazilas_present.id', '=', 'trainers.present_address_upazila_id')
                ->whereNull('loc_upazilas_present.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_divisions as loc_divisions_permanent', function ($join) {
            $join->on('loc_divisions_permanent.id', '=', 'trainers.permanent_address_division_id')
                ->whereNull('loc_divisions_permanent.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_districts as loc_districts_permanent', function ($join) {
            $join->on('loc_districts_permanent.id', '=', 'trainers.permanent_address_district_id')
                ->whereNull('loc_districts_permanent.deleted_at');
        });

        $trainerBuilder->leftJoin('loc_upazilas as loc_upazilas_permanent', function ($join) {
            $join->on('loc_upazilas_permanent.id', '=', 'trainers.permanent_address_upazila_id')
                ->whereNull('loc_upazilas_permanent.deleted_at');
        });

        $trainerBuilder->orderBy('trainers.id', $order);

        if (!empty($titleEn)) {
            $trainerBuilder->where('trainers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainerBuilder->where('trainers.title', 'like', '%' . $titleBn . '%');
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
        $response['_response_status'] = [
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


    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        $rules = [
            'institute_id' => [
                'exists:institutes,id,deleted_at,NULL',
                'required',
                'int',
            ],
            'branch_id' => [
                'exists:branches,id,deleted_at,NULL',
                'nullable',
                'int',
            ],
            'training_center_id' => [
                'exists:training_centers,id,deleted_at,NULL',
                'nullable',
                'int',
            ],
            'trainer_name' => [
                'required',
                'string',
                'max:500'
            ],
            'trainer_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'trainer_registration_number' => [
                'unique:trainers,trainer_registration_number,' . $id,
                'required',
                'string',
            ],
            'email' => [
                'unique:trainers,email,' . $id,
                'required',
                'email',
                'max:150',
            ],
            'mobile' => [
                'unique:trainers,mobile,' . $id,
                'required',
                BaseModel::MOBILE_REGEX,
                'max:15',
            ],
            'date_of_birth' => [
                'required',
                'date'
            ],
            'about_me' => [
                'nullable',
                'string'
            ],
            'about_me_en' => [
                'nullable',
                'string'
            ],
            'gender' => [
                'required',
                'integer'
            ],
            'marital_status' => [
                'required',
                'integer'
            ],
            'religion' => [
                'nullable',
                'integer'
            ],
            'nationality' => [
                'required',
                'string',
                'max:100'
            ],
            'nid' => [
                'nullable',
                'string',
                'max:30',
            ],
            'passport_number' => [
                'nullable',
                'string',
                'max:50'
            ],
            'educational_qualification' => [
                'nullable',
                'string'
            ],
            'educational_qualification_en' => [
                'nullable',
                'string'
            ],
            'skills' => [
                'nullable',
                'string'
            ],
            'skills_en' => [
                'nullable',
                'string'
            ],
            'present_address_division_id' => [
                'exists:loc_divisions,id,deleted_at,NULL',
                'nullable',
                'integer',

            ],
            'present_address_district_id' => [
                'exists:loc_districts,id,deleted_at,NULL',
                'nullable',
                'integer',

            ],
            'present_address_upazila_id' => [
                'exists:loc_upazilas,id,deleted_at,NULL',
                'nullable',
                'integer',

            ],
            'present_house_address' => [
                'nullable',
                'string'
            ],
            'present_house_address_en' => [
                'nullable',
                'string'
            ],
            'permanent_address_division_id' => [
                'exists:loc_divisions,id,deleted_at,NULL',
                'nullable',
                'integer',
            ],
            'permanent_address_district_id' => [
                'exists:loc_districts,id,deleted_at,NULL',
                'nullable',
                'integer',
            ],
            'permanent_address_upazila_id' => [
                'exists:loc_upazilas,id,deleted_at,NULL',
                'nullable',
                'integer',
            ],
            'permanent_house_address' => [
                'nullable',
                'string'
            ],
            'permanent_house_address_en' => [
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
            'created_by' => [
                'nullable',
                'integer',
            ],
            'updated_by' => [
                'nullable',
                'integer',
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    public function filterValidator(Request $request): Validator
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

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'trainer_name_en' => 'nullable|max:250|min:2',
            'trainer_name' => 'nullable|max:500|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'institute_id' => 'exists:institutes,id,deleted_at,NULL|int',
            'branch_id' => 'exists:branches,id,deleted_at,NULL|int',
            'training_center_id' => 'exists:training_centers,id,deleted_at,NULL|int',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
