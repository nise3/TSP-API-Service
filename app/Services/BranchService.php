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
    public function getBranchList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $rowStatus=$request->query('row_status');
        $limit = $request->query('limit', 10);
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Branch|Builder $branchBuilder */
        $branchBuilder = Branch::select([
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

        $branchBuilder->join("institutes",function($join) use($rowStatus){
            $join->on('branches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if(!is_null($rowStatus)){
                $join->where('institutes.row_status',$rowStatus);
            }
        });

        $branchBuilder->orderBy('branches.id', $order);

        if(!is_null($rowStatus)){
            $branchBuilder->where('branches.row_status',$rowStatus);
        }

        if (!empty($titleEn)) {
            $branchBuilder->where('branches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $branchBuilder->where('branches.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $branchBuilder */
        if (!is_null($paginate) || !is_null($limit)) {
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
        $response['order']=$order;
        $response['data']=$branches->toArray()['data'] ?? $branches->toArray();


        $response['response_status']= [
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
            'branches.id as id',
            'branches.title_en',
            'branches.title_bn',
            'branches.loc_division_id',
            'branches.loc_district_id',
            'branches.loc_upazila_id',
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

        $branchBuilder->join("institutes",function($join){
            $join->on('branches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
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


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
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

        return Validator::make($request->all(), $rules);
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
        $response['order']=$order;
        $response['data']=$branches->toArray()['data'] ?? $branches->toArray();


        $response['response_status']= [
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

}
