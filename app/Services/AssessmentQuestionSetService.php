<?php


namespace App\Services;


use App\Models\RplAssessmentQuestionSet;
use App\Models\BaseModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AssessmentQuestionSetService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAssessmentQuestionSetList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $assessmentId = $request['assessment_id'] ?? "";

        /** @var RplAssessmentQuestionSet|Builder $assessmentQuestionSetBuilder */
        $assessmentQuestionSetBuilder = RplAssessmentQuestionSet::select([
            'rpl_assessment_question_sets.id',
            'rpl_assessment_question_sets.title',
            'rpl_assessment_question_sets.title_en',
            'rpl_assessment_question_sets.assessment_id',

            'rpl_assessments.title as assessment_title',
            'rpl_assessments.title_en as assessment_title_en',
            'rpl_assessments.assessment_fee',
            'rpl_assessments.passing_score',

            'rpl_assessment_question_sets.row_status',
            'rpl_assessment_question_sets.created_at',
            'rpl_assessment_question_sets.updated_at',
            'rpl_assessment_question_sets.deleted_at'
        ]);

        $assessmentQuestionSetBuilder->orderBy('rpl_assessment_question_sets.id', $order);

        $assessmentQuestionSetBuilder->join('rpl_assessments', function ($join) {
            $join->on('rpl_assessments.id', '=', 'rpl_assessment_question_sets.assessment_id')
                ->whereNull('rpl_assessments.deleted_at');
        });

        if (!empty($titleEn)) {
            $assessmentQuestionSetBuilder->where('rpl_assessment_question_sets.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $assessmentQuestionSetBuilder->where('rpl_assessment_question_sets.title', 'like', '%' . $title . '%');
        }
        if (!empty($assessmentId)) {
            $assessmentQuestionSetBuilder->where('rpl_assessment_question_sets.assessment_id', $assessmentId);
        }

        /** @var Collection $assessmentQuestionSets */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $assessmentQuestionSets = $assessmentQuestionSetBuilder->paginate($pageSize);
            $paginateData = (object)$assessmentQuestionSets->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $assessmentQuestionSets = $assessmentQuestionSetBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $assessmentQuestionSets->toArray()['data'] ?? $assessmentQuestionSets->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplAssessmentQuestionSet
     */
    public function getOneAssessmentQuestionSet(int $id): RplAssessmentQuestionSet
    {
        /** @var RplAssessmentQuestionSet|Builder $assessmentQuestionSetBuilder */
        $assessmentQuestionSetBuilder = RplAssessmentQuestionSet::select([
            'rpl_assessment_question_sets.id',
            'rpl_assessment_question_sets.title',
            'rpl_assessment_question_sets.title_en',
            'rpl_assessment_question_sets.assessment_id',

            'rpl_assessments.id',
            'rpl_assessments.title as assessment_title',
            'rpl_assessments.title_en as assessment_title_en',
            'rpl_assessments.assessment_fee',
            'rpl_assessments.passing_score',

            'rpl_assessment_question_sets.row_status',
            'rpl_assessment_question_sets.created_at',
            'rpl_assessment_question_sets.updated_at',
            'rpl_assessment_question_sets.deleted_at'
        ]);

        $assessmentQuestionSetBuilder->where('rpl_assessment_question_sets.id', $id);

        $assessmentQuestionSetBuilder->join('rpl_assessments', function ($join) {
            $join->on('rpl_assessments.id', '=', 'rpl_assessment_question_sets.assessment_id')
                ->whereNull('rpl_assessments.deleted_at');
        });

        return $assessmentQuestionSetBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplAssessmentQuestionSet
     */
    public function store(array $data): RplAssessmentQuestionSet
    {
        $assessmentQuestionSet = app()->make(RplAssessmentQuestionSet::class);
        $assessmentQuestionSet->fill($data);
        $assessmentQuestionSet->save();
        return $assessmentQuestionSet;
    }

    /**
     * @param RplAssessmentQuestionSet $assessmentQuestionSet
     * @param array $data
     * @return RplAssessmentQuestionSet
     */
    public function update(RplAssessmentQuestionSet $assessmentQuestionSet, array $data): RplAssessmentQuestionSet
    {
        $assessmentQuestionSet->fill($data);
        $assessmentQuestionSet->save();
        return $assessmentQuestionSet;
    }

    /**
     * @param RplAssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function destroy(RplAssessmentQuestionSet $assessmentQuestionSet): bool
    {
        return $assessmentQuestionSet->delete();
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
            'assessment_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_assessments,id,deleted_at,NULL',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
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
