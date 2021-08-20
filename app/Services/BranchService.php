<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder;
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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Branch|Builder $branches */
        $branches = Branch::select([
            'branches.id as id',
            'branches.title_en',
            'branches.title_bn',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'branches.row_status',
            'branches.address',
            'branches.google_map_src',
            'branches.created_at',
            'branches.updated_at',
        ]);

        $branches->join('institutes', 'branches.institute_id', '=', 'institutes.id');
        $branches->orderBy('branches.id', $order);


        if (!empty($titleEn)) {
            $branches->where('branches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $branches->where('branches.title_bn', 'like', '%' . $titleBn . '%');
        }


        if ($paginate) {
            $branches = $branches->paginate(10);
            $paginateData = (object)$branches->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $branches = $branches->get();
        }

        $data = [];
        foreach ($branches as $branch) {
            $links['read'] = route('api.v1.branches.read', ['id' => $branch->id]);
            $links['update'] = route('api.v1.branches.update', ['id' => $branch->id]);
            $links['delete'] = route('api.v1.branches.destroy', ['id' => $branch->id]);
            $branch['_links'] = $links;
            $data[] = $branch->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,

                "search" => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.branches.get-list')

                ],

            ],

            "_page" => $page,
            "_order" => $order
        ];
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneBranch(int $id,  Carbon $startTime): array
    {
        /** @var Branch|Builder $branch */
        $branch = Branch::select([
            'branches.id as id',
            'branches.title_en',
            'branches.title_bn',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'branches.row_status',
            'branches.address',
            'branches.google_map_src',
            'branches.created_at',
            'branches.updated_at',
        ]);

        $branch->join('institutes', 'branches.institute_id', '=', 'institutes.id');
        $branch->where('branches.id', $id);
        $branch = $branch->first();

        $links = [];
        if (!empty($branch)) {
            $links['update'] = route('api.v1.branches.update', ['id' => $id]);
            $links['delete'] = route('api.v1.branches.destroy', ['id' => $id]);
        }

        return [
            "data" => $branch ?: null,
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => $links,
        ];

    }

    /**
     * @param array $data
     * @return branch
     */
    public function store(array $data): Branch
    {
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
//            'row_status' => [
//                'required_if:' . $id . ',==,null',
//                Rule::in([Branch::ROW_STATUS_ACTIVE, Branch::ROW_STATUS_INACTIVE]),
//            ],
        ];

        return Validator::make($request->all(), $rules);
    }

}
