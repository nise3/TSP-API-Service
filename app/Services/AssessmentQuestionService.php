<?php


namespace App\Services;


use App\Models\Assessment;
use App\Models\BaseModel;
use App\Models\AssessmentQuestion;
use App\Models\QuestionBank;
use App\Models\RplSector;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AssessmentQuestionService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getAssessmentQuestionList(array $request, Carbon $startTime, bool $isPublicApi = false): array
    {
        $titleEn = $request['title_en'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $assessmentId = $request['assessment_id'] ?? "";
        $rplLevelId = $request['rpl_level_id'] ?? "";
        $rplOccupationId = $request['rpl_occupation_id'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var AssessmentQuestion|Builder $assessmentQuestionBuilder */
        $assessmentQuestionBuilder = AssessmentQuestion::select([
            'assessment_questions.assessment_id',
            'assessments.title as assessment_title',
            'assessments.title_en as assessment_title_en',
            'assessment_questions.assessment_id',
            'assessment_questions.assessment_question_set_id',
            'assessment_questions.question_id',
            'question_banks.title as question_title',
            'question_banks.title_en as question_title_en',
            'assessment_questions.title',
            'assessment_questions.title_en',
            'assessment_questions.type',
            'assessment_questions.subject_id',
            'assessment_questions.option_1',
            'assessment_questions.option_1_en',
            'assessment_questions.option_2',
            'assessment_questions.option_2_en',
            'assessment_questions.option_3',
            'assessment_questions.option_3_en',
            'assessment_questions.option_4',
            'assessment_questions.option_4_en',
            'assessment_questions.answer',
            'assessment_questions.row_status',
            'assessment_questions.created_at',
            'assessment_questions.updated_at',
        ]);

        if (!$isPublicApi) {
            $assessmentQuestionBuilder->acl();
        }

        $assessmentQuestionBuilder->orderBy('assessment_questions.assessment_id', $order);

        $assessmentQuestionBuilder->join("assessments", function ($join) {
            $join->on('assessment_questions.assessment_id', '=', 'assessments.id')
                ->whereNull('assessments.deleted_at');
        });

        $assessmentQuestionBuilder->join("question_banks", function ($join) {
            $join->on('assessment_questions.question_id', '=', 'question_banks.id')
                ->whereNull('assessments.deleted_at');
        });
        if (!empty($titleEn)) {
            $assessmentQuestionBuilder->where('assessment_questions.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $assessmentQuestionBuilder->where('assessment_questions.title', 'like', '%' . $title . '%');
        }
        if ($isPublicApi) {
            $assessmentQuestionBuilder->where('assessments.rpl_level_id', $rplLevelId);
            $assessmentQuestionBuilder->where('assessments.rpl_occupation_id', $rplOccupationId);
            $assessmentSetIds = $assessmentQuestionBuilder->pluck('assessment_questions.assessment_question_set_id')->toArray();
            if(!empty($assessmentSetIds)){
                $randomAssessmentSetId = $assessmentSetIds[array_rand($assessmentSetIds)];
                $assessmentQuestionBuilder->where('assessment_questions.assessment_question_set_id', $randomAssessmentSetId);
            }

        }
        if (is_numeric($assessmentId)) {
            $assessmentQuestionBuilder->where('assessment_questions.assessment_id', $assessmentId);
        }
        if (is_numeric($rowStatus)) {
            $assessmentQuestionBuilder->where('assessment_questions.row_status', $rowStatus);
        }

        /** @var Collection $assessmentQuestions */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $assessmentQuestions = $assessmentQuestionBuilder->paginate($pageSize);
            $paginateData = (object)$assessmentQuestions->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $assessmentQuestions = $assessmentQuestionBuilder->get();
        }


        $response['order'] = $order;
        $response['data'] = $assessmentQuestions->toArray()['data'] ?? $assessmentQuestions->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param array $data
     */
    public function store(array $data)
    {
        foreach ($data['assessment_questions'] as $assessmentQuestion) {
            AssessmentQuestion::where('assessment_id', $assessmentQuestion['assessment_id'])->delete();
        }
        foreach ($data['assessment_questions'] as $assessmentQuestionData) {
            unset($assessmentQuestionData['id'], $assessmentQuestionData['difficulty_level'], $assessmentQuestionData['deleted_at'],);
            $assessmentQuestion = app(AssessmentQuestion::class);
            $assessmentQuestion->fill($assessmentQuestionData);
            $assessmentQuestion->save();
        }


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
            'assessment_questions' => [
                'required',
                'array',
                'min:1',
            ],
            'assessment_questions.*' => [
                Rule::requiredIf(!empty($data['assessment_questions'])),
                'nullable',
                'array',
                'min:1',
            ],
            'assessment_questions.*.assessment_id' => [
                'required',
                'int',
                'exists:assessments,id,deleted_at,NULL',
            ],
            'assessment_questions.*.assessment_question_set_id' => [
                'required',
                'int',
                'exists:assessments,id,deleted_at,NULL',
            ],
            'assessment_questions.*.question_id' => [
                'required',
                'int'
            ],
            'assessment_questions.*.title' => [
                'required',
                'string',
            ],
            'assessment_questions.*.title_en' => [
                'nullable',
                'string'
            ],
            'assessment_questions.*.type' => [
                'required',
                'int',
                Rule::in(AssessmentQuestion::TYPES)
            ],
            'assessment_questions.*.subject_id' => [
                'required',
                'int',
                'exists:subjects,id,deleted_at,NULL'
            ],
            'assessment_questions.*.answer' => [
                'required',
                'int',
                Rule::in(QuestionBank::ANSWERS)
            ],
            'assessment_questions.*.row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        $index = 0;
        foreach ($data['assessment_questions'] as $assessmentQuestion) {
            Log::info("Inside assessment_questions foreach");
            if ($assessmentQuestion['type'] == AssessmentQuestion::TYPE_MCQ) {
                Log::info("Inside TYPE_MCQ");
                $rules['assessment_questions.' . $index . '.option_1'] = [
                    'required',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_1_en'] = [
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_2'] = [
                    'required',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_2_en'] = [
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_3'] = [
                    'required',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_3_en'] = [
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_4'] = [
                    'required',
                    'string',
                    'max:600'
                ];
                $rules['assessment_questions.' . $index . '.option_4_en'] = [
                    'nullable',
                    'string',
                    'max:600'
                ];
            }
            ++$index;
        }
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
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'assessment_id' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "int"
            ],
        ], $customMessage);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function publicFilterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'assessment_id' => 'integer|gt:0',
            'rpl_level_id' => [
                'int',
                'required',
            ],
            'rpl_occupation_id' => [
                'int',
                'required',
            ],
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "int"
            ],
        ], $customMessage);
    }
}
