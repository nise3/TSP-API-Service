<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\ExamQuestionBank;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamQuestionBankService
{


    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();
        preg_match_all('/\[{2}(.*?)\]{2}/is', $data['title'], $match);
        $data['title'] = preg_replace('/\[{2}(.*?)\]{2}/is', '[[]]', $data['title']);
        $data['answer'] = $match[1];

        $rules = [
            'title' => [
                'required',
                'string',
            ],
            'title_en' => [
                'nullable',
                'string'
            ],
            'question_type' => [
                'required',
                'int',
                Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
            ],
            'accessor_type' => [
                'required',
                'string',
                'max:100'
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
            'answer' => [
                Rule::requiredIf(!empty($data['question_type']) && array_key_exists($data['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPE)),
                'nullable',
                'array',
            ],
            'answer.*' => [
                'nullable',
                'string',
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
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
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
