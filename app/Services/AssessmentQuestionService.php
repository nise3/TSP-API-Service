<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\AssessmentQuestion;
use App\Models\RplSector;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var AssessmentQuestion|Builder $questionBankBuilder */
        $questionBankBuilder = AssessmentQuestion::select([
            'assessment_questions.id',
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
            'assessment_questions.difficulty_level',
            'assessment_questions.answer',
            'assessment_questions.row_status',
            'assessment_questions.created_at',
            'assessment_questions.updated_at',
            'assessment_questions.deleted_at',
        ]);

        if (!$isPublicApi) {
            $questionBankBuilder->acl();
        }

        $questionBankBuilder->orderBy('assessment_questions.id', $order);

        if (!empty($titleEn)) {
            $questionBankBuilder->where('assessment_questions.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $questionBankBuilder->where('assessment_questions.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $questionBanks */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $questionBanks = $questionBankBuilder->paginate($pageSize);
            $paginateData = (object)$questionBanks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $questionBanks = $questionBankBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $questionBanks->toArray()['data'] ?? $questionBanks->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplSector
     */
    public function getOneAssessmentQuestion(int $id): AssessmentQuestion
    {
        /** @var AssessmentQuestion|Builder $questionBankBuilder */
        $questionBankBuilder = AssessmentQuestion::select([
            'assessment_questions.id',
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
            'assessment_questions.difficulty_level',
            'assessment_questions.answer',
            'assessment_questions.row_status',
            'assessment_questions.created_at',
            'assessment_questions.updated_at',
            'assessment_questions.deleted_at',
        ]);

        if (is_numeric($id)) {
            $questionBankBuilder->where('assessment_questions.id', $id);
        }

        return $questionBankBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return AssessmentQuestion
     */
    public function store(array $data): AssessmentQuestion
    {
        $questionBank = app()->make(AssessmentQuestion::class);
        $questionBank->fill($data);
        $questionBank->save();
        return $questionBank;
    }

    /**
     * @param AssessmentQuestion $questionBank
     * @param array $data
     * @return AssessmentQuestion
     */
    public function update(AssessmentQuestion $questionBank, array $data): AssessmentQuestion
    {
        $questionBank->fill($data);
        $questionBank->save();
        return $questionBank;
    }

    /**
     * @param AssessmentQuestion $questionBank
     * @return bool
     */
    public function destroy(AssessmentQuestion $questionBank): bool
    {
        return $questionBank->delete();
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
            'assessment_questions.*.assessment_questions.*.option_1' => [
                'required',
                'string',
                'max:600'
            ],
            'assessment_questions.*.option_1_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'assessment_questions.*.option_2' => [
                'required',
                'string',
                'max:600'
            ],
            'assessment_questions.*.option_2_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'assessment_questions.*.option_3' => [
                'required',
                'string',
                'max:600'
            ],
            'assessment_questions.*.option_3_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'assessment_questions.*.option_4' => [
                'required',
                'string',
                'max:600'
            ],
            'assessment_questions.*.option_4_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'assessment_questions.*.answer' => [
                'required',
                'int'
            ],
            'assessment_questions.*.row_status' => [
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
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
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
