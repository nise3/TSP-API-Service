<?php


namespace App\Services;
use App\Traits\Scopes\ScopeRowStatusTrait;
use App\Models\Rank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
/**
 * Class RankService
 * @package App\Services
 */
class RankService
{
    /**
     *
     * @param Request $request
     * @return array
     */

    public function getRankList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $ranks = Rank::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.order',
                'organizations.title_en as organization_title_en',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        )->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id')
            ->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id')
            ->orderBy('ranks.id', $order);
        if (!empty($titleEn)) {
            $ranks->where('ranks.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $ranks->where('ranks.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $ranks = $ranks->paginate(10);
            $paginate_data = (object)$ranks->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $ranks = $ranks->get();
        }
        foreach ($ranks as $rank) {
            $_links['read'] = route('api.v1.ranks.read', ['id' => $rank->id]);
            $_links['update'] = route('api.v1.ranks.update', ['id' => $rank->id]);
            $_links['delete'] = route('api.v1.ranks.destroy', ['id' => $rank->id]);
            $rank['_links'] = $_links;
            $data[] = $rank->toArray();
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

                "search" => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.ranks.get-list')

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
    public function getOneRank($id)
    {
        $startTime = Carbon::now();
        $rank = Rank::select(
            [
                'ranks.id',
                'ranks.title_en',
                'ranks.title_bn',
                'ranks.grade',
                'ranks.order',
                'organizations.title_en as organization_title_en',
                'rank_types.title_en as rank_type_title_en',
                'ranks.row_status',
                'ranks.created_at',
                'ranks.updated_at',
            ]
        )->leftJoin('organizations', 'ranks.organization_id', '=', 'organizations.id')
            ->join('rank_types', 'ranks.rank_type_id', '=', 'rank_types.id')
            ->where('ranks.row_status', '=', Rank::ROW_STATUS_ACTIVE)
            ->where('ranks.id', '=', $id);

        $rank = $rank->first();

        $links = [];
        if (!empty($rank)) {
            $links['update'] = route('api.v1.ranks.update', ['id' => $id]);
            $links['delete'] = route('api.v1.ranks.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $rank ? $rank : null,
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
     * @param array $data
     * @return Rank
     */
    public function store(array $data): Rank
    {
        $rank = new Rank();
        $rank->fill($data);
        $rank->save();

        return $rank;
    }

    /**
     * @param Rank $rank
     * @param array $data
     * @return Rank
     */
    public function update(Rank $rank, array $data): Rank

    {
        $rank->fill($data);
        $rank->save();
        return $rank;
    }

    /**
     * @param Rank $rank
     * @return Rank
     */
    public function destroy(Rank $rank): Rank
    {
        $rank->row_status = 99;
        $rank->save();
        return $rank;
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
            'rank_type_id' => [
                'required',
                'int',
                'exists:rank_types,id',
            ],
            'grade' => [
                'nullable',
                'string',
                'max:100',
            ],
            'order' => [
                'nullable',
                'int',
            ],
            'organization_id' => [
                'nullable',
                'int',
                'exists:organizations,id',
            ],
        ];
        return Validator::make($request->all(), $rules);
    }


}
