<?php


namespace App\Services;


use App\Models\AssessmentQuestionSet;
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

        /** @var AssessmentQuestionSet|Builder $assessmentQuestionSetBuilder */
        $assessmentQuestionSetBuilder = AssessmentQuestionSet::select([
            'assessment_question_sets.id',
            'assessment_question_sets.title',
            'assessment_question_sets.title_en',
            'assessment_question_sets.assessment_id',

            'assessments.id',
            'assessments.title as assessmentTitle',
            'assessments.title_en as assessmentTitleEn',
            'assessments.assessment_fee',
            'assessments.passing_score',

            'assessment_question_sets.row_status',
            'assessment_question_sets.created_at',
            'assessment_question_sets.updated_at',
            'assessment_question_sets.deleted_at'
        ]);

        $assessmentQuestionSetBuilder->orderBy('assessment_question_sets.id', $order);

        $assessmentQuestionSetBuilder->join('assessments', function ($join) {
            $join->on('assessments.id', '=', 'assessment_question_sets.assessment_id')
                ->whereNull('assessments.deleted_at');
        });

        if (!empty($titleEn)) {
            $assessmentQuestionSetBuilder->where('assessment_question_sets.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $assessmentQuestionSetBuilder->where('assessment_question_sets.title', 'like', '%' . $title . '%');
        }
        if (!empty($assessmentId)) {
            $assessmentQuestionSetBuilder->where('assessment_question_sets.assessment_id', $assessmentId);
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
     * @return AssessmentQuestionSet
     */
    public function getOneAssessmentQuestionSet(int $id): AssessmentQuestionSet
    {
        /** @var AssessmentQuestionSet|Builder $assessmentQuestionSetBuilder */
        $assessmentQuestionSetBuilder = AssessmentQuestionSet::select([
            'assessment_question_sets.id',
            'assessment_question_sets.title',
            'assessment_question_sets.title_en',
            'assessment_question_sets.assessment_id',

            'assessments.id',
            'assessments.title as assessmentTitle',
            'assessments.title_en as assessmentTitleEn',
            'assessments.assessment_fee',
            'assessments.passing_score',

            'assessment_question_sets.row_status',
            'assessment_question_sets.created_at',
            'assessment_question_sets.updated_at',
            'assessment_question_sets.deleted_at'
        ]);

        $assessmentQuestionSetBuilder->where('assessment_question_sets.id', $id);

        $assessmentQuestionSetBuilder->join('assessments', function ($join) {
            $join->on('assessments.id', '=', 'assessment_question_sets.assessment_id')
                ->whereNull('assessments.deleted_at');
        });

        return $assessmentQuestionSetBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return AssessmentQuestionSet
     */
    public function store(array $data): AssessmentQuestionSet
    {
        $assessmentQuestionSet = app()->make(AssessmentQuestionSet::class);
        $assessmentQuestionSet->fill($data);
        $assessmentQuestionSet->save();
        return $assessmentQuestionSet;
    }

    /**
     * @param AssessmentQuestionSet $assessmentQuestionSet
     * @param array $data
     * @return AssessmentQuestionSet
     */
    public function update(AssessmentQuestionSet $assessmentQuestionSet, array $data): AssessmentQuestionSet
    {
        $assessmentQuestionSet->fill($data);
        $assessmentQuestionSet->save();
        return $assessmentQuestionSet;
    }

    /**
     * @param AssessmentQuestionSet $assessmentQuestionSet
     * @return bool
     */
    public function destroy(AssessmentQuestionSet $assessmentQuestionSet): bool
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
                'exists:assessments,id,deleted_at,NULL',
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
