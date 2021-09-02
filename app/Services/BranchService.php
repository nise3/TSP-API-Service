<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BranchService
 * @package App\Services
 */
class BranchService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getBranchList(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $instituteId = array_key_exists('institute_id', $request) ? $request['institute_id'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var Branch|Builder $branchBuilder */
        $branchBuilder = Branch::select([
            'branches.id',
            'branches.title_en',
            'branches.title_bn',
            'branches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'branches.loc_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'branches.loc_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'branches.loc_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'branches.row_status',
            'branches.address',
            'branches.google_map_src',
            'branches.row_status',
            'branches.created_at',
            'branches.updated_at',
        ]);

        $branchBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('branches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $branchBuilder->leftJoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('loc_divisions.id', '=', 'branches.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_divisions.row_status', $rowStatus);
            }
        });

        $branchBuilder->leftJoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('loc_districts.id', '=', 'branches.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_districts.row_status', $rowStatus);
            }
        });

        $branchBuilder->leftJoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('loc_upazilas.id', '=', 'branches.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_upazilas.row_status', $rowStatus);
            }
        });


        $branchBuilder->orderBy('branches.id', $order);

        if (is_numeric($rowStatus)) {
            $branchBuilder->where('branches.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $branchBuilder->where('branches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $branchBuilder->where('branches.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($instituteId) {
            $branchBuilder->where('branches.institute_id', '=', $instituteId);
        }

        /** @var Collection $branchBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $branches = $branchBuilder->paginate($pageSize);
            $paginateData = (object)$branches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $branches = $branchBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $branches->toArray()['data'] ?? $branches->toArray();


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
    public function getOneBranch(int $id, Carbon $startTime): array
    {
        /** @var Branch|Builder $branchBuilder */
        $branchBuilder = Branch::select([
            'branches.id',
            'branches.title_en',
            'branches.title_bn',
            'branches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'branches.loc_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'branches.loc_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'branches.loc_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'branches.row_status',
            'branches.address',
            'branches.google_map_src',
            'branches.row_status',
            'branches.created_at',
            'branches.updated_at',
        ]);

        $branchBuilder->join("institutes", function ($join) {
            $join->on('branches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $branchBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'branches.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $branchBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'branches.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $branchBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'branches.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $branchBuilder->where('branches.id', $id);

        /** @var Branch $branchBuilder */
        $branch = $branchBuilder->first();

        return [
            "data" => $branch ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];

    }

    /**
     * @param array $data
     * @return branch
     */
    public function store(array $data): Branch
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $branch = new Branch();
        $branch->fill($data);
        $branch->save();
        return $branch;
    }

    /**
     * @param Branch $branch
     * @param array $data
     * @return Branch
     */
    public function update(Branch $branch, array $data): Branch
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $branch->fill($data);
        $branch->save();
        return $branch;

    }

    /**
     * @param Branch $branch
     * @return bool
     */
    public function destroy(Branch $branch): bool
    {
        return $branch->delete();
    }



    public function parseGoogleMapSrc(?string $googleMapSrc): ?string
    {
        if (!empty($googleMapSrc) && preg_match('/src="([^"]+)"/', $googleMapSrc, $match)) {
            $googleMapSrc = $match[1];
        }
        return $googleMapSrc;
    }

    public function getBranchTrashList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $limit = $request->query('limit', 10);
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Branch|Builder $branchBuilder */
        $branchBuilder = Branch::onlyTrashed()->select([
            'branches.id as id',
            'branches.title_en',
            'branches.title_bn',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'branches.row_status',
            'branches.address',
            'branches.google_map_src',
            'branches.row_status',
            'branches.created_at',
            'branches.updated_at',
        ]);

        $branchBuilder->join('institutes', 'branches.institute_id', '=', 'institutes.id');
        $branchBuilder->orderBy('branches.id', $order);


        if (!empty($titleEn)) {
            $branchBuilder->where('branches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $branchBuilder->where('branches.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $branchBuilder */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $branches = $branchBuilder->paginate($limit);
            $paginateData = (object)$branches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $branches = $branchBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $branches->toArray()['data'] ?? $branches->toArray();


        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    public function restore(Branch $branch): bool
    {
        return $branch->restore();
    }

    public function forceDelete(Branch $branch): bool
    {
        return $branch->forceDelete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 600',
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id',
            ],
            'address' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'google_map_src' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        return Validator::make($request->all(), $rules, $customMessage);
    }

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
