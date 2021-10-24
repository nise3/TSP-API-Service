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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getBranchList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Branch|Builder $branchBuilder */
        $branchBuilder = Branch::select([
            'branches.id',
            'branches.title_en',
            'branches.title',
            'branches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'branches.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'branches.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'branches.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'branches.address',
            'branches.address_en',
            'branches.google_map_src',
            'branches.row_status',
            'branches.created_by',
            'branches.updated_by',
            'branches.created_at',
            'branches.updated_at',
            'branches.deleted_at',
        ]);

        $branchBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('branches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }*/
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


        $branchBuilder->orderBy('branches.id', $order);

        if (is_numeric($rowStatus)) {
            $branchBuilder->where('branches.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $branchBuilder->where('branches.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $branchBuilder->where('branches.title', 'like', '%' . $titleBn . '%');
        }

        if (!empty($instituteId)) {
            $branchBuilder->where('branches.institute_id', '=', $instituteId);
        }

        /** @var Collection $branches */
        if (!empty($paginate) || !empty($pageSize)) {
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
            'branches.title',
            'branches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'branches.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'branches.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'branches.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'branches.address',
            'branches.address_en',
            'branches.google_map_src',
            'branches.row_status',
            'branches.created_by',
            'branches.updated_by',
            'branches.created_at',
            'branches.updated_at',
            'branches.deleted_at',
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

        /** @var Branch $branch */
        $branch = $branchBuilder->firstOrFail();

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
        $titleBn = $request->query('title');
        $paginate = $request->query('page');
        $limit = $request->query('limit', 10);

        $order = $request->filled('order') ? $request->query('order') : 'ASC';

        /** @var Branch|Builder $branchBuilder */
        $branchBuilder = Branch::onlyTrashed()->select([
            'branches.id as id',
            'branches.title_en',
            'branches.title',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'branches.address',
            'branches.address_en',
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
            $branchBuilder->where('branches.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $branchBuilder */
        if (!empty($paginate) || !empty($limit)) {
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
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max: 600',
                'min:2'
            ],
            'institute_id' => [
                'required',
                'exists:institutes,id,deleted_at,NULL',
                'int'
            ],
            'address' => [
                'nullable',
                'string'
            ],
            'address_en' => [
                'nullable',
                'string'
            ],
            'google_map_src' => [
                'nullable',
                'string'
            ],
            'loc_division_id' => ['nullable', 'integer'],
            'loc_district_id' => ['nullable', 'integer'],
            'loc_upazila_id' => ['nullable', 'integer'],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];

        return Validator::make($request->all(), $rules, $customMessage);
    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        return Validator::make($request->all(), [
            'title_en' => 'nullable|max:250|min:2',
            'title' => 'nullable|max:600|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'institute_id' => 'nullable|int|exists:institutes,id,deleted_at,NULL',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

}
