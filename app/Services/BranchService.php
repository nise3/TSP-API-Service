<?php


namespace App\Services;

use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
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

        $branchBuilder->join('institutes', 'branches.institute_id', '=', 'institutes.id');
        $branchBuilder->orderBy('branches.id', $order);


        if (!empty($titleEn)) {
            $branchBuilder->where('branches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $branchBuilder->where('branches.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $branchBuilder */
        if ($paginate) {
            $branches = $branchBuilder->paginate(10);
            $paginateData = (object)$branches->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $branches = $branchBuilder->get();
        }


        return [
            "data" => $branches->toArray() ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
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
    public function getOneBranch(int $id, Carbon $startTime): array
    {
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

        $branchBuilder->join('institutes', 'branches.institute_id', '=', 'institutes.id');
        $branchBuilder->where('branches.id', $id);

        /** @var Branch $branchBuilder */
        $branch = $branchBuilder->first();

        return [
            "data" => $branch ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
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
                'required_if:' . $id . ',==,null',
                Rule::in([Branch::ROW_STATUS_ACTIVE, Branch::ROW_STATUS_INACTIVE]),
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

}
