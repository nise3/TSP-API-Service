<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\ExamQuestionBank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ExamQuestionBankService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getQuestionBankList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $subjectId = $request['subject_id'] ?? "";

        /** @var ExamQuestionBank|Builder $examQuestionBankBuilder */
        $examQuestionBankBuilder = ExamQuestionBank::select([
            'exam_question_banks.id',
            'exam_question_banks.title',
            'exam_question_banks.title_en',
            'exam_question_banks.accessor_type',
            'exam_question_banks.accessor_id',
            'exam_question_banks.subject_id',
            'exam_subjects.title as exam_subject_title',
            'exam_subjects.title_en as exam_subject_title_en',
            'exam_question_banks.question_type',
            'exam_question_banks.option_1',
            'exam_question_banks.option_1_en',
            'exam_question_banks.option_2',
            'exam_question_banks.option_2_en',
            'exam_question_banks.option_3',
            'exam_question_banks.option_3_en',
            'exam_question_banks.option_4',
            'exam_question_banks.option_4_en',
            'exam_question_banks.answers',
            'exam_question_banks.row_status',
            'exam_question_banks.created_at',
            'exam_question_banks.updated_at',
        ]);

        $examQuestionBankBuilder->acl();

        $examQuestionBankBuilder->orderBy('exam_question_banks.id', $order);

        $examQuestionBankBuilder->join('exam_subjects', function ($join) {
            $join->on('exam_question_banks.subject_id', '=', 'exam_subjects.id')
                ->whereNull('exam_subjects.deleted_at');
        });

        if (!empty($titleEn)) {
            $examQuestionBankBuilder->where('exam_question_banks.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $examQuestionBankBuilder->where('exam_question_banks.title', 'like', '%' . $title . '%');
        }

        if (!empty($subjectId)) {
            $examQuestionBankBuilder->where('exam_question_banks.subject_id', $subjectId);
        }

        /** @var Collection $questionBanks */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $questionBanks = $examQuestionBankBuilder->paginate($pageSize);
            $paginateData = (object)$questionBanks->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $questionBanks = $examQuestionBankBuilder->get();
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
     * @return Model|Builder
     */
    public function getOneExamQuestionBank(int $id): Model|Builder
    {
        /** @var ExamQuestionBank|Builder $examQuestionBankBuilder */
        $examQuestionBankBuilder = ExamQuestionBank::select([
            'exam_question_banks.id',
            'exam_question_banks.title',
            'exam_question_banks.title_en',
            'exam_question_banks.accessor_type',
            'exam_question_banks.accessor_id',
            'exam_question_banks.subject_id',
            'exam_subjects.title as exam_subject_title',
            'exam_subjects.title_en as exam_subject_title_en',
            'exam_question_banks.question_type',
            'exam_question_banks.option_1',
            'exam_question_banks.option_1_en',
            'exam_question_banks.option_2',
            'exam_question_banks.option_2_en',
            'exam_question_banks.option_3',
            'exam_question_banks.option_3_en',
            'exam_question_banks.option_4',
            'exam_question_banks.option_4_en',
            'exam_question_banks.answers',
            'exam_question_banks.row_status',
            'exam_question_banks.created_at',
            'exam_question_banks.updated_at',
        ]);
        $examQuestionBankBuilder->join('exam_subjects', function ($join) {
            $join->on('exam_question_banks.subject_id', '=', 'exam_subjects.id')
                ->whereNull('exam_subjects.deleted_at');
        });

        $examQuestionBankBuilder->where('exam_question_banks.id', $id);

        return $examQuestionBankBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return ExamQuestionBank
     */
    public function store(array $data): ExamQuestionBank
    {
        $examQuestionBank = app(ExamQuestionBank::class);
        $examQuestionBank->fill($data);
        $examQuestionBank->save();

        return $examQuestionBank;
    }

    /**
     * @param ExamQuestionBank $examQuestionBank
     * @param array $data
     * @return ExamQuestionBank
     */
    public function update(ExamQuestionBank $examQuestionBank, array $data): ExamQuestionBank
    {
        $examQuestionBank->fill($data);
        $examQuestionBank->save();

        return $examQuestionBank;
    }


    /**
     * @param ExamQuestionBank $examQuestionBank
     * @return bool
     */

    public function destroy(ExamQuestionBank $examQuestionBank): bool
    {
        return $examQuestionBank->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if(!empty($data["question_type"]) && $data["question_type"]==ExamQuestionBank::EXAM_QUESTION_TYPE_Fill_IN_THE_BLANKS){
            preg_match_all('/\[{2}(.*?)\]{2}/is', $data['title'], $match);
            $data['title'] = preg_replace('/\[{2}(.*?)\]{2}/is', '[[]]', $data['title']);
            $data['answers'] = $match[1];
        }

        $rules = [
            'title' => [
                'required',
                'string',
            ],
            'title_en' => [
                'nullable',
                'string'
            ],
            'accessor_type' => [
                'required',
                'string',
                'max:100',
                Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
            ],
            'accessor_id' => [
                'required',
                'int',
                'min:1'
            ],
            'subject_id' => [
                'required',
                'int',
                'exists:exam_subjects,id,deleted_at,NULL'
            ],
            'question_type' => [
                'required',
                'int',
                Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
            ],
            'option_1' => [
                Rule::requiredIf(!empty($data['question_type']) && $data['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ],
            'option_1_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'option_2' => [
                Rule::requiredIf(!empty($data['question_type']) && $data['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ],
            'option_2_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'option_3' => [
                Rule::requiredIf(!empty($data['question_type']) && $data['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ],
            'option_3_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'option_4' => [
                Rule::requiredIf(!empty($data['question_type']) && $data['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ],
            'option_4_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'answers' => [
                Rule::requiredIf(!empty($data['question_type']) && array_key_exists($data['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                'nullable',
                'array',
            ],
            'answers.*' => [
                'nullable',
                'string',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return Validator::make($request->all(), [
            'title_en' => 'nullable',
            'title' => 'nullable',
            'subject_id' => 'nullable|min:1',
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
