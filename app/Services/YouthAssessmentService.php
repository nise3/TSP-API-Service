<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\YouthAssessment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class YouthAssessmentService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthAssessmentList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rplSectorId = $request['rpl_occupation_id'] ?? "";

        /** @var YouthAssessment|Builder $youthAssessmentBuilder */
        $youthAssessmentBuilder = YouthAssessment::select([
            'youth_assessments.id',
            'youth_assessments.youth_id',
            'youth_assessments.assessment_id',
            'youth_assessments.rto_batch_id',
            'youth_assessments.result',
            'youth_assessments.score',

            'youth_assessments.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'youth_assessments.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'youth_assessments.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'youth_assessments.rto_country_id',
            'rto_countries.title_en as rto_country_title_en',
            'rto_countries.title as rto_country_title',

            'youth_assessments.target_country_id',
            'target_countries.title_en as target_country_title_en',
            'target_countries.title as target_country_title',

            'youth_assessments.rto_id',
            'registered_training_organizations.title_en as rto_title_en',
            'registered_training_organizations.title as rto_title',

            'youth_assessments.created_at',
            'youth_assessments.updated_at',
            'youth_assessments.deleted_at',
        ]);

        $youthAssessmentBuilder->orderBy('youth_assessments.id', $order);

        $youthAssessmentBuilder->join('rpl_occupations', function ($join){
            $join->on('youth_assessments.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_levels', function ($join){
            $join->on('youth_assessments.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_sectors', function ($join){
            $join->on('youth_assessments.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as rto_countries', function ($join){
            $join->on('youth_assessments.rto_country_id', '=', 'rto_countries.id')
                ->whereNull('rto_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as target_countries', function ($join){
            $join->on('youth_assessments.target_country_id', '=', 'target_countries.id')
                ->whereNull('target_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('registered_training_organizations', function ($join){
            $join->on('youth_assessments.rto_id', '=', 'registered_training_organizations.id')
                ->whereNull('registered_training_organizations.deleted_at');
        });

        if (!empty($titleEn)) {
            $youthAssessmentBuilder->where('youth_assessments.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $youthAssessmentBuilder->where('youth_assessments.title', 'like', '%' . $title . '%');
        }
        if (!empty($rplSectorId)) {
            $youthAssessmentBuilder->where('youth_assessments.rpl_occupation_id', $rplSectorId);
        }

        /** @var Collection $youthAssessments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $youthAssessments = $youthAssessmentBuilder->paginate($pageSize);
            $paginateData = (object)$youthAssessments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthAssessments = $youthAssessmentBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $youthAssessments->toArray()['data'] ?? $youthAssessments->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return YouthAssessment
     */
    public function getOneYouthAssessment(int $id): YouthAssessment
    {
        /** @var YouthAssessment|Builder $youthAssessmentBuilder */
        $youthAssessmentBuilder = YouthAssessment::select([
            'youth_assessments.id',
            'youth_assessments.youth_id',
            'youth_assessments.assessment_id',
            'youth_assessments.rto_batch_id',
            'youth_assessments.result',
            'youth_assessments.score',

            'youth_assessments.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'youth_assessments.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'youth_assessments.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'youth_assessments.rto_country_id',
            'rto_countries.title_en as rto_country_title_en',
            'rto_countries.title as rto_country_title',

            'youth_assessments.target_country_id',
            'target_countries.title_en as target_country_title_en',
            'target_countries.title as target_country_title',

            'youth_assessments.rto_id',
            'registered_training_organizations.title_en as rto_title_en',
            'registered_training_organizations.title as rto_title',

            'youth_assessments.created_at',
            'youth_assessments.updated_at',
            'youth_assessments.deleted_at',
        ]);

        if (is_numeric($id)) {
            $youthAssessmentBuilder->where('youth_assessments.id', $id);
        }

        $youthAssessmentBuilder->join('rpl_occupations', function ($join){
            $join->on('youth_assessments.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_levels', function ($join){
            $join->on('youth_assessments.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_sectors', function ($join){
            $join->on('youth_assessments.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as rto_countries', function ($join){
            $join->on('youth_assessments.rto_country_id', '=', 'rto_countries.id')
                ->whereNull('rto_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as target_countries', function ($join){
            $join->on('youth_assessments.target_country_id', '=', 'target_countries.id')
                ->whereNull('target_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('registered_training_organizations', function ($join){
            $join->on('youth_assessments.rto_id', '=', 'registered_training_organizations.id')
                ->whereNull('registered_training_organizations.deleted_at');
        });

        return $youthAssessmentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return YouthAssessment
     */
    public function store(array $data): YouthAssessment
    {
        $youthAssessment = app()->make(YouthAssessment::class);
        $youthAssessment->fill($data);
        $youthAssessment->save();
        return $youthAssessment;
    }

    /**
     * @param YouthAssessment $youthAssessment
     * @param array $data
     * @return YouthAssessment
     */
    public function update(YouthAssessment $youthAssessment, array $data): YouthAssessment
    {
        $youthAssessment->fill($data);
        $youthAssessment->save();
        return $youthAssessment;
    }

    /**
     * @param YouthAssessment $youthAssessment
     * @return bool
     */
    public function destroy(YouthAssessment $youthAssessment): bool
    {
        return $youthAssessment->delete();
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
            'rpl_sector_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_sectors,id,deleted_at,NULL',
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
                'exists:rpl_levels,id,deleted_at,NULL',
            ],
            'youth_id' => [
                'required',
                'int',
                'min:1',
            ],
            'assessment_id' => [
                'required',
                'int',
                'min:1',
                'exists:assessments,id,deleted_at,NULL',
            ],
            'target_country_id' => [
                'required',
                'int',
                'min:1',
                'exists:rto_countries,country_id',
            ],
            'rto_country_id' => [
                'required',
                'int',
                'min:1',
                'exists:rto_countries,country_id',
            ],
            'rto_id' => [
                'required',
                'int',
                'min:1',
                'rtos:registered_training_organizations,id,deleted_at,NULL',
            ],
            'rto_batch_id' => [
                'nullable',
                'int',
                'min:1',
                'exists:rto_batches,id,deleted_at,NULL',
            ],
            'answers' => [
                'required',
                'array',
                'min:1'
            ],
            'answers.*' => [
                Rule::requiredIf(!empty($data['answers'])),
                'array',
                'min:1'
            ],
            'answers.*.question_id' => [
                Rule::requiredIf(!empty($data['answers'])),
                'int',
                'min:1'
            ],
            'answers.*.answer' => [
                Rule::requiredIf(!empty($data['answers'])),
                'int',
                Rule::in([1,2,3,4])
            ]
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

    public function assignToBatchValidator(Request $request, int $id)
    {
        $data = $request->all();

        $rules = [
            'rto_batch_id' => [
                'required',
                'int',
                'min:1',
                'exists:rto_batches,id,deleted_at,NULL',
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }
}
