<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\Assessment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AssessmentService
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

        /** @var Assessment|Builder $assessmentBuilder */
        $assessmentBuilder = Assessment::select([
            'assessments.id',
            'assessments.title',
            'assessments.title_en',
            'assessments.assessment_fee',
            'assessments.passing_score',

            'assessments.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_occupations.rpl_sector_id as rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'assessments.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',


            'assessments.created_at',
            'assessments.updated_at',
            'assessments.deleted_at',
        ]);

        $assessmentBuilder->orderBy('assessments.id', $order);

        $assessmentBuilder->join('rpl_occupations', function ($join){
            $join->on('assessments.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $assessmentBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $assessmentBuilder->join('rpl_levels', function ($join){
            $join->on('assessments.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        if (!empty($titleEn)) {
            $assessmentBuilder->where('assessments.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $assessmentBuilder->where('assessments.title', 'like', '%' . $title . '%');
        }
        if (!empty($rplOccupationId)) {
            $assessmentBuilder->where('assessments.rpl_occupation_id', $rplOccupationId);
        }
        if (!empty($rplSectorId)) {
            $assessmentBuilder->where('rpl_occupations.rpl_sector_id', $rplSectorId);
        }
        if (!empty($rplLevelId)) {
            $assessmentBuilder->where('assessments.rpl_level_id', $rplLevelId);
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
     * @return Assessment
     */
    public function getOneAssessment(int $id): Assessment
    {
        /** @var Assessment|Builder $assessmentBuilder */
        $assessmentBuilder = Assessment::select([
            'assessments.id',
            'assessments.title',
            'assessments.title_en',
            'assessments.passing_score',
            'assessments.assessment_fee',

            'assessments.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_occupations.rpl_sector_id as rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'assessments.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'assessments.created_at',
            'assessments.updated_at',
            'assessments.deleted_at',
        ]);

        $assessmentBuilder->where('assessments.id', $id);

        $assessmentBuilder->join('rpl_occupations', function ($join){
            $join->on('assessments.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $assessmentBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $assessmentBuilder->join('rpl_levels', function ($join){
            $join->on('assessments.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        return $assessmentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Assessment
     */
    public function store(array $data): Assessment
    {
        $assessment = app()->make(Assessment::class);
        $assessment->fill($data);
        $assessment->save();
        return $assessment;
    }

    /**
     * @param Assessment $assessment
     * @param array $data
     * @return Assessment
     */
    public function update(Assessment $assessment, array $data): Assessment
    {
        $assessment->fill($data);
        $assessment->save();
        return $assessment;
    }

    /**
     * @param Assessment $assessment
     * @return bool
     */
    public function destroy(Assessment $assessment): bool
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
                Rule::unique('assessments', 'rpl_level_id')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) use($data) {
                        return $query->where('assessments.rpl_occupation_id', $data['rpl_occupation_id']);
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
