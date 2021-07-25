<?php


namespace App\Services;


use App\Models\Branch;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BranchService
{
    public function getBranchList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        $branches = Branch::select([
            'branches.id as id',
            'branches.title_en',
            'branches.title_bn',
            'institutes.title_en as institute_title_en',
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
            $paginate_data = (object)$branches->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $branches = $branches->get();
        }

        $data = [];
        foreach ($branches as $branch) {
            $_links['read'] = route('api.v1.branches.read', ['id' => $branch->id]);
            $_links['update'] = route('api.v1.branches.update', ['id' => $branch->id]);
            $_links['delete'] = route('api.v1.branches.destroy', ['id' => $branch->id]);
            $branch['_links'] = $_links;
            $data[] = $branch->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => [
                'paginate' => $paginate_link,

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

    public function getOneBranch($id): array
    {
        $startTime = Carbon::now();

        $branch = Branch::select([
            'branches.id as id',
            'branches.title_en',
            'branches.title_bn',
            'institutes.title_en as institute_title_en',
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
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
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

    public function update(Branch $branch, array $data): Branch
    {
        $branch->fill($data);
        $branch->save();

        return $branch;

    }

    public function destroy(Branch $branch): Branch
    {
        $branch->row_status = Branch::ROW_STATUS_DELETED;
        $branch->save();

        return $branch;
    }


    /**
     * @param Request $request
     * @param null $id
     * @return Validator
     */
    public function validator(Request $request): Validator
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
                'max: 191',
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id',
            ],
            'address' => [
                'nullable',
                'string',
                'max:191'
            ],
            'google_map_src' => [
                'nullable',
                'string'
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

}
