<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\RplAssessment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RplAssessmentService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAssessmentList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rplOccupationId = $request['rpl_occupation_id'] ?? "";
        $rplSectorId = $request['rpl_sector_id'] ?? "";
        $rplLevelId = $request['rpl_level_id'] ?? "";

        /** @var RplAssessment|Builder $assessmentBuilder */
        $assessmentBuilder = RplAssessment::select([
            'rpl_assessments.id',
            'rpl_assessments.title',
            'rpl_assessments.title_en',
            'rpl_assessments.assessment_fee',
            'rpl_assessments.passing_score',

            'rpl_assessments.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_occupations.rpl_sector_id as rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'rpl_assessments.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',


            'rpl_assessments.created_at',
            'rpl_assessments.updated_at',
            'rpl_assessments.deleted_at',
        ]);

        $assessmentBuilder->orderBy('rpl_assessments.id', $order);

        $assessmentBuilder->join('rpl_occupations', function ($join){
            $join->on('rpl_assessments.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $assessmentBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $assessmentBuilder->join('rpl_levels', function ($join){
            $join->on('rpl_assessments.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        if (!empty($titleEn)) {
            $assessmentBuilder->where('rpl_assessments.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $assessmentBuilder->where('rpl_assessments.title', 'like', '%' . $title . '%');
        }
        if (!empty($rplOccupationId)) {
            $assessmentBuilder->where('rpl_assessments.rpl_occupation_id', $rplOccupationId);
        }
        if (!empty($rplSectorId)) {
            $assessmentBuilder->where('rpl_occupations.rpl_sector_id', $rplSectorId);
        }
        if (!empty($rplLevelId)) {
            $assessmentBuilder->where('rpl_assessments.rpl_level_id', $rplLevelId);
        }

        /** @var Collection $assessments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $assessments = $assessmentBuilder->paginate($pageSize);
            $paginateData = (object)$assessments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $assessments = $assessmentBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $assessments->toArray()['data'] ?? $assessments->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplAssessment
     */
    public function getOneAssessment(int $id): RplAssessment
    {
        /** @var RplAssessment|Builder $assessmentBuilder */
        $assessmentBuilder = RplAssessment::select([
            'rpl_assessments.id',
            'rpl_assessments.title',
            'rpl_assessments.title_en',
            'rpl_assessments.passing_score',
            'rpl_assessments.assessment_fee',

            'rpl_assessments.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_occupations.rpl_sector_id as rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'rpl_assessments.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'rpl_assessments.created_at',
            'rpl_assessments.updated_at',
            'rpl_assessments.deleted_at',
        ]);

        $assessmentBuilder->where('rpl_assessments.id', $id);

        $assessmentBuilder->join('rpl_occupations', function ($join){
            $join->on('rpl_assessments.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $assessmentBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $assessmentBuilder->join('rpl_levels', function ($join){
            $join->on('rpl_assessments.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        return $assessmentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplAssessment
     */
    public function store(array $data): RplAssessment
    {
        $assessment = app()->make(RplAssessment::class);
        $assessment->fill($data);
        $assessment->save();
        return $assessment;
    }

    /**
     * @param RplAssessment $assessment
     * @param array $data
     * @return RplAssessment
     */
    public function update(RplAssessment $assessment, array $data): RplAssessment
    {
        $assessment->fill($data);
        $assessment->save();
        return $assessment;
    }

    /**
     * @param RplAssessment $assessment
     * @return bool
     */
    public function destroy(RplAssessment $assessment): bool
    {
        return $assessment->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $rules = [
            'title' => [
                'required',
                'string',
                'max:600',
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'rpl_occupation_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_occupations,id,deleted_at,NULL',
            ],
            'rpl_level_id' => [
                'required',
                'int',
                'min:1',
                Rule::unique('rpl_assessments', 'rpl_level_id')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) use($data) {
                        return $query->where('rpl_assessments.rpl_occupation_id', $data['rpl_occupation_id']);
                    }),
                'exists:rpl_levels,id,deleted_at,NULL',
            ],
            'passing_score' => [
                'required',
                'int',
                'min:1',
                'max:100',
            ],
            'assessment_fee' => [
                'required',
                'numeric'
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }


    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'assessment_id' => 'nullable|int',
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
        ], $customMessage);
    }
}
