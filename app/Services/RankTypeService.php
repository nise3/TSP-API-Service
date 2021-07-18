<?php


namespace App\Services;

use App\Models\RankType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Class RankTypeService
 * @package App\Services
 */
class RankTypeService
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getRankTypeList(Request $request): array
    {

        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $startTime = Carbon::now();
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $rankTypes = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'organizations.title_en as organization_title_en',
                'rank_types.row_status',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        )->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id')
            ->orderBy('rank_types.id', $order);


        if (!empty($titleEn)) {
            $rankTypes->where('rank_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $rankTypes->where('rank_types.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $rankTypes = $rankTypes->paginate(10);
            $paginate_data = (object)$rankTypes->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $rankTypes = $rankTypes->get();
        }
        $data = [];
        foreach ($rankTypes as $rankType) {
            $_links['read'] = route('api.v1.rank-types.read', ['id' => $rankType->id]);
            $_links['edit'] = route('api.v1.rank-types.update', ['id' => $rankType->id]);
            $_links['delete'] = route('api.v1.rank-types.destroy', ['id' => $rankType->id]);
            $rankType['_links'] = $_links;
            $data[] = $rankType->toArray();
        }

        $response = [
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
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.rank-types.get-list')
                ],
            ],
                "_page" => $page,
                "_order" => $order
        ];

        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneRanktype($id): array
    {
        $startTime = Carbon::now();
        $rankType = RankType::select(
            [
                'rank_types.id',
                'rank_types.title_en',
                'rank_types.title_bn',
                'organizations.title_en as organization_title_en',
                'rank_types.row_status',
                'rank_types.created_at',
                'rank_types.updated_at',
            ]
        );
        $rankType->leftJoin('organizations', 'rank_types.organization_id', '=', 'organizations.id');
        $rankType->where('rank_types.id', '=', $id);
        $rankType->where('rank_types.row_status', '=', 1);
        $rankType = $rankType->first();

        $links = [];
        if (!empty($rankType)) {
            $links['update'] = route('api.v1.rank-types.update', ['id' => $id]);
            $links['delete'] = route('api.v1.rank-types.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $rankType ? $rankType : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
        return $response;

    }

    /**
     * @param $data
     * @return RankType
     */
    public function store($data): RankType
    {
        $rankType = new RankType();
        $rankType->fill($data);
        $rankType->save();

        return $rankType;
    }

    /**
     * @param RankType $rankType
     * @param array $data
     * @return RankType
     */
    public function update(RankType $rankType, array $data): RankType

    {
        $rankType->fill($data);
        $rankType->save();
        return $rankType;
    }


    /**
     * @param RankType $rankType
     * @return RankType
     */
    public function destroy(RankType $rankType): RankType
    {
        $rankType->row_status = 99;
        $rankType->save();
        return $rankType;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request)
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
            'organization_id' => [
                'nullable',
                'int',
                'exists:organizations,id', //always check for foreign key
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
        return Validator::make($request->all(), $rules);
    }


}
