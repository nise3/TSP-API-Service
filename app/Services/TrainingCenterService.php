<?php

namespace App\Services;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
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
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainingCenterList(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $instituteId = array_key_exists('institute_id', $request) ? $request['institute_id'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::select([
            'training_centers.id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.loc_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'training_centers.loc_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'training_centers.loc_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'training_centers.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'training_centers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title_bn as branch_title_bn',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);

        $trainingCentersBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });
        $trainingCentersBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }
        });

        $trainingCentersBuilder->orderBy('training_centers.id', $order);

        if (is_numeric($rowStatus)) {
            $trainingCentersBuilder->where('training_centers.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $trainingCentersBuilder->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainingCentersBuilder->where('training_centers.title_bn', 'like', '%' . $titleBn . '%');
        }

        $trainingCentersBuilder->leftJoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('loc_divisions.id', '=', 'training_centers.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_divisions.row_status', $rowStatus);
            }
        });

        $trainingCentersBuilder->leftJoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('loc_districts.id', '=', 'training_centers.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_districts.row_status', $rowStatus);
            }
        });

        $trainingCentersBuilder->leftJoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('loc_upazilas.id', '=', 'training_centers.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_upazilas.row_status', $rowStatus);
            }
        });

        if ($instituteId) {
            $trainingCentersBuilder->where('training_centers.institute_id', '=', $instituteId);
        }

        if (is_numeric($rowStatus)) {
            $trainingCentersBuilder->where('training_centers.row_status', $rowStatus);
        }

        /** @var Collection $trainingCentersBuilder */
        if (!is_null($paginate) || !is_null($pageSize)) {
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
    public function getOneTrainingCenter(int $id, Carbon $startTime): array
    {
        /** @var TrainingCenter|Builder $trainingCenterBuilder */
        $trainingCenterBuilder = TrainingCenter::select([
            'training_centers.id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.loc_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'training_centers.loc_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'training_centers.loc_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'training_centers.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'training_centers.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title_bn as branch_title_bn',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);

        $trainingCenterBuilder->join("institutes", function ($join) {
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $trainingCenterBuilder->leftJoin("branches", function ($join) {
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        if (!empty($id)) {
            $trainingCenterBuilder->where('training_centers.id', '=', $id);
        }

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

        /** @var TrainingCenter $trainingCenterBuilder */
        $trainingCenter = $trainingCenterBuilder->first();


        return [
            "data" => $trainingCenter ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
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
        return $trainingCenter;
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
     * @return Validator
     */
    public function validator(Request $request, $id = null): Validator
    {
        $rules = [
            'title_en' => 'required|string|max: 191',
            'title_bn' => 'required|string|max: 1000',
            'institute_id' => 'required|int|exists:institutes,id',
            'branch_id' => 'nullable|int|exists:branches,id',
            'center_location_type' => 'nullable|int',
            'address' => ['nullable', 'string', 'max:1000'],
            'google_map_src' => ['nullable', 'string'],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    /**
     * @param string|null $googleMapSrc
     * @return string
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
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::onlyTrashed()->select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.institute_id',
            'institutes.title_en as institute_name',
            'training_centers.branch_id',
            'branches.title_en as branch_name',
            'training_centers.address',
            'training_centers.address',
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
            $trainingCentersBuilder->where('training_centers.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $trainingCentersBuilder */
        if ($paginate || $limit) {
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
        $response['response_status'] = [
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
