<?php


namespace App\Services;

use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class SkillService
 * @package App\Services
 */
class SkillService
{

    /**
     * @param Request $request
     * @return array
     */
    public function getSkillList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $skills = Skill::select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'organizations.title_en as organization_title_en',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
            ]
        )->LeftJoin('organizations', 'skills.organization_id', '=', 'organizations.id')
            ->orderBy('skills.id', $order);

        if (!empty($titleEn)) {
            $skills->where('skills.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $skills->where('skills.title_bn', 'like', '%' . $titleBn . '%');
        }


        if ($paginate) {
            $skills = $skills->paginate(10);
            $paginate_data = (object)$skills->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $skills = $skills->get();
        }

        $data = [];

        foreach ($skills as $skill) {
            $_links['read'] = route('api.v1.skills.read', ['id' => $skill->id]);
            $_links['update'] = route('api.v1.skills.update', ['id' => $skill->id]);
            $_links['delete'] = route('api.v1.skills.destroy', ['id' => $skill->id]);
            $skill['_links'] = $_links;
            $data[] = $skill->toArray();
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
                    '_link' => route('api.v1.skills.get-list')

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
    public function getOneSkill($id)
    {
        $startTime = Carbon::now();
        $skill = Skill::select(
            [
                'skills.id as id',
                'skills.title_en',
                'skills.title_bn',
                'organizations.title_en as organization_title_en',
                'skills.row_status',
                'skills.created_at',
                'skills.updated_at',
            ]
        );
        $skill->LeftJoin('organizations', 'skills.organization_id', '=', 'organizations.id')
            ->where('skills.row_status', '=', Skill::ROW_STATUS_ACTIVE)
            ->where('skills.id', '=', $id);
        $skill = $skill->first();

        $links = [];
        if (!empty($skill)) {
            $links['update'] = route('api.v1.skills.update', ['id' => $id]);
            $links['delete'] = route('api.v1.skills.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $skill ? $skill : null,
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
     * @return Skill
     */
    public function store(array $data): Skill
    {
        $skill = new Skill();
        $skill->fill($data);
        $skill->save();

        return $skill;
    }

    /**
     * @param Skill $skill
     * @param array $data
     * @return Skill
     */
    public function update(Skill $skill, array $data): Skill
    {
        $skill->fill($data);
        $skill->save();

        return $skill;
    }

    /**
     * @param Skill $skill
     * @return Skill
     */
    public function destroy(Skill $skill): Skill
    {
        $skill->row_status = 99;
        $skill->save();

        return $skill;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
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
                'exists:organizations,id',
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
