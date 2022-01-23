<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrainingCenterService
 * @package App\Services
 */
class TrainingCenterService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainingCenterList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $branchId = $request['branch_id'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $locDistrictId = $request['loc_district_id'] ?? "";
        $locUpzilaId = $request['loc_upazila_id'] ?? "";


        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::select([
            'training_centers.id',
            'training_centers.center_location_type',
            'training_centers.title_en',
            'training_centers.title',
            'training_centers.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'training_centers.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'training_centers.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'training_centers.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institutetitle',
            'training_centers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at',
            'training_centers.deleted_at',
        ])->acl();

        $trainingCentersBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCentersBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $trainingCentersBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'training_centers.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $trainingCentersBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'training_centers.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $trainingCentersBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'training_centers.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });
        $trainingCentersBuilder->orderBy('training_centers.id', $order);

        if (is_numeric($instituteId)) {
            $trainingCentersBuilder->where('training_centers.institute_id', '=', $instituteId);
        }
        if (is_numeric($branchId)) {
            $trainingCentersBuilder->where('training_centers.branch_id', '=', $branchId);
        }
        if (is_numeric($rowStatus)) {
            $trainingCentersBuilder->where('training_centers.row_status', '=', $rowStatus);
        }

        if (!empty($titleEn)) {
            $trainingCentersBuilder->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $trainingCentersBuilder->where('training_centers.title', 'like', '%' . $title . '%');
        }

        if (!empty($skillIds)) {
            $trainingCentersBuilder->join('training_center_skill', 'training_center_skill.training_center_id', '=', 'training_centers.id');
            $trainingCentersBuilder->whereIn('training_center_skill.skill_id', $skillIds);
        }

        if (is_numeric($locUpzilaId)) {
            $trainingCentersBuilder->where('training_centers.loc_upazila_id', '=', $locUpzilaId);
        } else if (is_numeric($locDistrictId)) {
            $trainingCentersBuilder->where('training_centers.loc_district_id', '=', $locDistrictId);
        }

        $trainingCentersBuilder->groupBy('training_centers.id');


        /** @var Collection $trainingCentersBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainingCenters = $trainingCentersBuilder->paginate($pageSize);
            $paginateData = (object)$trainingCenters->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenters = $trainingCentersBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainingCenters->toArray()['data'] ?? $trainingCenters->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getPublicTrainingCenterList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $branchId = $request['branch_id'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $locDistrictId = $request['loc_district_id'] ?? "";
        $locUpzilaId = $request['loc_upazila_id'] ?? "";


        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::select([
            'training_centers.id',
            'training_centers.center_location_type',
            'training_centers.title_en',
            'training_centers.title',
            'training_centers.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'training_centers.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'training_centers.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'training_centers.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institutetitle',
            'training_centers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at',
            'training_centers.deleted_at',
        ]);

        $trainingCentersBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }*/
        });
        $trainingCentersBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }*/
        });

        $trainingCentersBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'training_centers.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $trainingCentersBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'training_centers.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $trainingCentersBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'training_centers.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });
        $trainingCentersBuilder->orderBy('training_centers.id', $order);

        if (is_numeric($instituteId)) {
            $trainingCentersBuilder->where('training_centers.institute_id', '=', $instituteId);
        }
        if (is_numeric($branchId)) {
            $trainingCentersBuilder->where('training_centers.branch_id', '=', $branchId);
        }
        if (is_numeric($rowStatus)) {
            $trainingCentersBuilder->where('training_centers.row_status', '=', $rowStatus);
        }

        if (!empty($titleEn)) {
            $trainingCentersBuilder->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $trainingCentersBuilder->where('training_centers.title', 'like', '%' . $title . '%');
        }

        if (!empty($skillIds)) {
            $trainingCentersBuilder->join('training_center_skill', 'training_center_skill.training_center_id', '=', 'training_centers.id');
            $trainingCentersBuilder->whereIn('training_center_skill.skill_id', $skillIds);
        }

        if (is_numeric($locUpzilaId)) {
            $trainingCentersBuilder->where('training_centers.loc_upazila_id', '=', $locUpzilaId);
        } else if (is_numeric($locDistrictId)) {
            $trainingCentersBuilder->where('training_centers.loc_district_id', '=', $locDistrictId);
        }

        $trainingCentersBuilder->groupBy('training_centers.id');


        /** @var Collection $trainingCentersBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainingCenters = $trainingCentersBuilder->paginate($pageSize);
            $paginateData = (object)$trainingCenters->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenters = $trainingCentersBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainingCenters->toArray()['data'] ?? $trainingCenters->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return TrainingCenter
     */
    public function getOneTrainingCenter(int $id): TrainingCenter
    {
        /** @var TrainingCenter|Builder $trainingCenterBuilder */
        $trainingCenterBuilder = TrainingCenter::select([
            'training_centers.id',
            'training_centers.center_location_type',
            'training_centers.title_en',
            'training_centers.title',
            'training_centers.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'training_centers.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'training_centers.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'training_centers.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'training_centers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'training_centers.address',
            'training_centers.address_en',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at',
            'training_centers.deleted_at',
        ]);

        $trainingCenterBuilder->join("institutes", function ($join) {
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $trainingCenterBuilder->leftJoin("branches", function ($join) {
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });


        $trainingCenterBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'training_centers.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $trainingCenterBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'training_centers.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $trainingCenterBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'training_centers.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $trainingCenterBuilder->where('training_centers.id', '=', $id);
        /** @var TrainingCenter $trainingCenter */
        return $trainingCenterBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return TrainingCenter
     */
    public function store(array $data): TrainingCenter
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $trainingCenter = new TrainingCenter();
        $trainingCenter->fill($data);
        $trainingCenter->Save();
        if (!empty($data['skill_ids'])) {
            $this->assignSkills($trainingCenter, $data['skill_ids']);
        }
        return $trainingCenter;
    }

    public function assignSkills($trainingCenter, $skills)
    {
        $skills = Skill::whereIn("id", $skills)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $trainingCenter->skills()->sync($skills);
    }

    /**
     * @param TrainingCenter $trainingCenter
     * @param array $data
     * @return TrainingCenter
     */
    public function update(TrainingCenter $trainingCenter, array $data): TrainingCenter
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $trainingCenter->fill($data);
        $trainingCenter->Save();
        if (!empty($data['skill_ids'])) {
            $this->assignSkills($trainingCenter, $data['skill_ids']);
        }
        return $trainingCenter;
    }

    /**
     * @param TrainingCenter $trainingCenter
     * @return bool
     */
    public function destroy(TrainingCenter $trainingCenter): bool
    {
        return $trainingCenter->delete();
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, $id = null): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('skill_ids')) {
            $skill_ids = is_array($request->get('skill_ids')) ? $request->get('skill_ids') : explode(',', $request->get('skill_ids'));
            $request->offsetSet('skill_ids', $skill_ids);
        }

        $customMessage = [
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        $rules = [
            'institute_id' => 'required|exists:institutes,id,deleted_at,NULL|int',
            'branch_id' => 'nullable|exists:branches,id,deleted_at,NULL|int',
            'center_location_type' => [
                'sometimes',
                'required',
                'int',
                Rule::in(TrainingCenter::CENTER_LOCATION_TYPES)
            ],
            'title' => 'required|string|max: 1000',
            'title_en' => 'nullable|string|max: 500',
            'loc_division_id' => ['nullable', 'integer'],
            'loc_district_id' => ['nullable', 'integer'],
            'loc_upazila_id' => ['nullable', 'integer'],
            'location_latitude' => ['nullable', 'string'],
            'location_longitude' => ['nullable', 'string'],
            'google_map_src' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'address_en' => ['nullable', 'string'],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
            'skill_ids' => [
                'nullable',
                'array',
                'min:1',
                'max:10'
            ],
            'skill_ids.*' => [
                'nullable',
                'integer',
                'distinct',
                'min:1'
            ]
        ];
        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param string|null $googleMapSrc
     * @return string|null
     */
    public function parseGoogleMapSrc(?string $googleMapSrc): ?string
    {
        if (!empty($googleMapSrc) && preg_match('/src="([^"]+)"/', $googleMapSrc, $match)) {
            $googleMapSrc = $match[1];
        }
        return $googleMapSrc;
    }

    public function getTrainingCenterTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::onlyTrashed()->select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title as title',
            'training_centers.institute_id',
            'institutes.title_en as institute_name',
            'institutes.title as institute_name_bn',
            'training_centers.branch_id',
            'branches.title_en as branch_name',
            'branches.title as branch_name_bn',
            'training_centers.address',
            'training_centers.address_en',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCentersBuilder->join('institutes', 'training_centers.institute_id', '=', 'institutes.id');
        $trainingCentersBuilder->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');

        if (!empty($titleEn)) {
            $trainingCentersBuilder->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainingCentersBuilder->where('training_centers.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $trainingCentersBuilder */
        if (is_numeric($paginate) || is_numeric($limit)) {
            $limit = $limit ?: 10;
            $trainingCenters = $trainingCentersBuilder->paginate($limit);
            $paginateData = (object)$trainingCenters->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenters = $trainingCentersBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $trainingCenters->toArray()['data'] ?? $trainingCenters->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    public function restore(TrainingCenter $trainingCenter): bool
    {
        return $trainingCenter->restore();
    }

    public function forceDelete(TrainingCenter $trainingCenter): bool
    {
        return $trainingCenter->forceDelete();
    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('skill_ids')) {
            $skill_ids = is_array($request->get('skill_ids')) ? $request->get('skill_ids') : explode(',', $request->get('skill_ids'));
            $request->offsetSet('skill_ids', $skill_ids);
        }

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        $rules = [
            'title_en' => 'nullable|max:500|min:2',
            'title' => 'nullable|max:1000|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'institute_id' => 'nullable|exists:institutes,id,deleted_at,NULL|int',
            'branch_id' => 'nullable|exists:branches,id,deleted_at,NULL|int',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'skill_ids' => [
                'nullable',
                'array',
                'min:1',
                'max:10'
            ],
            'skill_ids.*' => [
                'nullable',
                'integer',
                'distinct',
                'min:1'
            ],
            'loc_district_id' => [
                'nullable',
                'integer'
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer'
            ]
        ];

        return Validator::make($request->all(), $rules, $customMessage);
    }
}
