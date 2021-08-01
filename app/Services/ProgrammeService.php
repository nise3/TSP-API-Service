<?php


namespace App\Services;


use Illuminate\Http\Request;
use App\Models\Programme;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

/**
 * Class ProgrammeService
 * @package App\Services
 */
class ProgrammeService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getProgrammeList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Programme|Builder $programmes */
        $programmes = Programme::select([
            'programmes.id as id',
            'programmes.title_en',
            'programmes.title_bn',
            'institutes.title_en as institute_title_en',
            'programmes.code as programme_code',
            'programmes.logo as programme_logo',
            'programmes.description',
            'programmes.row_status',
            'programmes.created_at',
            'programmes.updated_at',
        ]);
        $programmes->join('institutes', 'programmes.institute_id', '=', 'institutes.id');
        $programmes->orderBy('programmes.id', $order);

        if (!empty($titleEn)) {
            $programmes->where('programmes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $programmes->where('programmes.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $programmes = $programmes->paginate(10);
            $paginateData = (object)$programmes->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $programmes = $programmes->get();
        }

        $data = [];
        foreach ($programmes as $programme) {
            $links['read'] = route('api.v1.programmes.read', ['id' => $programme->id]);
            $links['update'] = route('api.v1.programmes.update', ['id' => $programme->id]);
            $links['delete'] = route('api.v1.programmes.destroy', ['id' => $programme->id]);
            $programme['_links'] = $links;
            $data[] = $programme->toArray();
        }
        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
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
                    '_link' => route('api.v1.programmes.get-list')
                ],
            ],
            "_page" => $page,
            "_order" => $order
        ];
    }

    /**
     * @param $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneProgramme($id, Carbon $startTime): array
    {
        /** @var Programme|Builder $programme */
        $programme = Programme::select([
            'programmes.id as id',
            'programmes.title_en',
            'programmes.title_bn',
            'institutes.title_en as institute_title_en',
            'programmes.code as programme_code',
            'programmes.logo as programme_logo',
            'programmes.row_status',
            'programmes.description',
            'programmes.created_at',
            'programmes.updated_at',
        ]);
        $programme->join('institutes', 'programmes.institute_id', '=', 'institutes.id');
        $programme->where('programmes.id','=', $id);
        $programme = $programme->first();

        $links = [];
        if (!empty($programme)) {
            $links['update'] = route('api.v1.programmes.update', ['id' => $id]);
            $links['delete'] = route('api.v1.programmes.destroy', ['id' => $id]);
        }

        return [
            "data" => $programme ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => $links,
        ];
    }

    /**
     * @param array $data
     * @return Programme
     */
    public function store(array $data): Programme
    {
        $programme = new Programme();
        $programme->fill($data);
        $programme->Save();
        return $programme;
    }

    /**
     * @param Programme $programme
     * @param array $data
     * @return Programme
     */
    public function update(Programme $programme, array $data): Programme
    {
        $programme->fill($data);
        $programme->save();
        return $programme;
    }

    /**
     * @param Programme $programme
     * @return Programme
     */
    public function destroy(Programme $programme): Programme
    {
        $programme->row_status = Programme::ROW_STATUS_DELETED;
        $programme->save();
        return $programme;
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Validator
     */
    public function validator(Request $request, $id = null): Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:191'
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id'
            ],
            'code' => [
                'required',
                'string',
                'max:191',
                'unique:programmes,code,' . $id,
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'logo' => [
                'nullable',
                'string',
                'max:191',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([Programme::ROW_STATUS_ACTIVE, Programme::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
