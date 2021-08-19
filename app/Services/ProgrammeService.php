<?php

namespace App\Services;

use App\Helpers\Classes\CustomPaginateResonse;
use Illuminate\Http\Request;
use App\Models\Programme;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

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
        $response=[];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 2);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Programme|Builder $programmesBuilder */
        $programmesBuilder = Programme::select([
            'programmes.id as id',
            'programmes.title_en',
            'programmes.title_bn',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'programmes.code as programme_code',
            'programmes.logo as programme_logo',
            'programmes.description',
            'programmes.row_status',
            'programmes.created_at',
            'programmes.updated_at',
        ]);
        $programmesBuilder->join('institutes', 'programmes.institute_id', '=', 'institutes.id');
        $programmesBuilder->orderBy('programmes.id', $order);

        if (!empty($titleEn)) {
            $programmesBuilder->where('programmes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $programmesBuilder->where('programmes.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $programmesBuilder */
        if ($paginate) {
            $programmes = $programmesBuilder->paginate($limit);
            $paginateData = (object)$programmes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $programmes = $programmesBuilder->get();
        }
        $response['order']=$order;
        $response['data']=$programmes->toArray()['data'] ?? $programmes->toArray();
        $response['response_status']= [
            "success" => true,
            "code" => Response::HTTP_OK,
            "started" => $startTime,
            "finished" => Carbon::now()->format('s'),
        ];

        return $response;

    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneProgramme(int $id, Carbon $startTime): array
    {
        /** @var Programme|Builder $programmeBuilder */
        $programmeBuilder = Programme::select([
            'programmes.id as id',
            'programmes.title_en',
            'programmes.title_bn',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'programmes.code as programme_code',
            'programmes.logo as programme_logo',
            'programmes.row_status',
            'programmes.description',
            'programmes.created_at',
            'programmes.updated_at',
        ]);
        $programmeBuilder->join('institutes', 'programmes.institute_id', '=', 'institutes.id');
        $programmeBuilder->where('programmes.id', '=', $id);

        /** @var Programme $programmeBuilder */
        $programme = $programmeBuilder->first();
        $links = [];
        return [
            "data" => $programme ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ]
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
     * @return bool
     */
    public function destroy(Programme $programme): bool
    {
        return $programme->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
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
                'max:1000'
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
                'required_if:' . $id . ',==,null',
                Rule::in([Programme::ROW_STATUS_ACTIVE, Programme::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
