<?php


namespace App\Services;


use App\Models\RplAssessment;
use App\Models\BaseModel;
use App\Models\RplAssessmentQuestion;
use App\Models\RplQuestionBank;
use App\Models\RplSector;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RplAssessmentQuestionService
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
        $assessmentQuestionId = $request['assessment_question_set_id'] ?? "";
        $rplLevelId = $request['rpl_level_id'] ?? "";
        $rplOccupationId = $request['rpl_occupation_id'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var RplAssessmentQuestion|Builder $assessmentQuestionBuilder */
        $assessmentQuestionBuilder = RplAssessmentQuestion::select([
            'rpl_assessment_questions.assessment_id',
            'rpl_assessments.title as assessment_title',
            'rpl_assessments.title_en as assessment_title_en',
            'rpl_assessment_questions.assessment_id',
            'rpl_assessment_questions.assessment_question_set_id',
            'rpl_assessment_questions.question_id',
            'rpl_question_banks.title as question_title',
            'rpl_question_banks.title_en as question_title_en',
            'rpl_assessment_questions.title',
            'rpl_assessment_questions.title_en',
            'rpl_assessment_questions.type',
            'rpl_assessment_questions.subject_id',
            'rpl_assessment_questions.option_1',
            'rpl_assessment_questions.option_1_en',
            'rpl_assessment_questions.option_2',
            'rpl_assessment_questions.option_2_en',
            'rpl_assessment_questions.option_3',
            'rpl_assessment_questions.option_3_en',
            'rpl_assessment_questions.option_4',
            'rpl_assessment_questions.option_4_en',
            'rpl_assessment_questions.row_status',
            'rpl_assessment_questions.created_at',
            'rpl_assessment_questions.updated_at',
        ]);

        if (!$isPublicApi) {
            /** Answer will not be shown in public api question list */
            $assessmentQuestionBuilder->addSelect('rpl_assessment_questions.answer');
            $assessmentQuestionBuilder->acl();
        }

        $assessmentQuestionBuilder->orderBy('rpl_assessment_questions.assessment_id', $order);

        $assessmentQuestionBuilder->join("rpl_assessments", function ($join) {
            $join->on('rpl_assessment_questions.assessment_id', '=', 'rpl_assessments.id')
                ->whereNull('rpl_assessments.deleted_at');
        });

        $assessmentQuestionBuilder->join("rpl_question_banks", function ($join) {
            $join->on('rpl_assessment_questions.question_id', '=', 'rpl_question_banks.id')
                ->whereNull('rpl_assessments.deleted_at');
        });
        if (!empty($titleEn)) {
            $assessmentQuestionBuilder->where('rpl_assessment_questions.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $assessmentQuestionBuilder->where('rpl_assessment_questions.title', 'like', '%' . $title . '%');
        }
        if ($isPublicApi) {
            $assessmentQuestionBuilder->where('rpl_assessments.rpl_level_id', $rplLevelId);
            $assessmentQuestionBuilder->where('rpl_assessments.rpl_occupation_id', $rplOccupationId);
            $assessmentSetIds = $assessmentQuestionBuilder->pluck('rpl_assessment_questions.assessment_question_set_id')->toArray();
            if (!empty($assessmentSetIds)) {
                $randomAssessmentSetId = $assessmentSetIds[array_rand($assessmentSetIds)];
                $assessmentQuestionBuilder->where('rpl_assessment_questions.assessment_question_set_id', $randomAssessmentSetId);
            }

        }
        if (is_numeric($assessmentId)) {
            $assessmentQuestionBuilder->where('rpl_assessment_questions.assessment_id', $assessmentId);
        }
        if (is_numeric($assessmentQuestionId)) {
            $assessmentQuestionBuilder->where('rpl_assessment_questions.assessment_question_set_id', $assessmentQuestionId);
        }
        if (is_numeric($rowStatus)) {
            $assessmentQuestionBuilder->where('rpl_assessment_questions.row_status', $rowStatus);
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
            RplAssessmentQuestion::where('assessment_id', $assessmentQuestion['assessment_id'])
                ->where('assessment_question_set_id', $assessmentQuestion['assessment_question_set_id'])
                ->delete();
        }
        foreach ($data['assessment_questions'] as $assessmentQuestionData) {
            unset($assessmentQuestionData['id'], $assessmentQuestionData['difficulty_level'], $assessmentQuestionData['deleted_at']);
            $assessmentQuestion = app(RplAssessmentQuestion::class);
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
                'exists:rpl_assessments,id,deleted_at,NULL',
            ],
            'assessment_questions.*.assessment_question_set_id' => [
                'required',
                'int',
                'exists:rpl_assessment_question_sets,id,deleted_at,NULL',
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
                Rule::in(RplAssessmentQuestion::TYPES)
            ],
            'assessment_questions.*.subject_id' => [
                'required',
                'int',
                'exists:rpl_subjects,id,deleted_at,NULL'
            ],
            'assessment_questions.*.answer' => [
                'required',
                'int',
                Rule::in(RplQuestionBank::ANSWERS)
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
            if ($assessmentQuestion['type'] == RplAssessmentQuestion::TYPE_MCQ) {
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
            'assessment_question_set_id' => 'integer|gt:0',
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
