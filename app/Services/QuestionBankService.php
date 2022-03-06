<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\QuestionBank;
use App\Models\RplSector;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class QuestionBankService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getQuestionBankList(array $request, Carbon $startTime, bool $isPublicApi = false): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $subjectId = $request['subject_id'] ?? "";

        /** @var QuestionBank|Builder $questionBankBuilder */
        $questionBankBuilder = QuestionBank::select([
            'question_banks.id',
            'question_banks.title',
            'question_banks.title_en',
            'question_banks.type',
            'question_banks.subject_id',
            'question_banks.option_1',
            'question_banks.option_1_en',
            'question_banks.option_2',
            'question_banks.option_2_en',
            'question_banks.option_3',
            'question_banks.option_3_en',
            'question_banks.option_4',
            'question_banks.option_4_en',
            'question_banks.difficulty_level',
            'question_banks.answer',
            'question_banks.row_status',
            'question_banks.created_at',
            'question_banks.updated_at',
            'question_banks.deleted_at',
        ]);

        if (!$isPublicApi) {
            $questionBankBuilder->acl();
        }

        $questionBankBuilder->orderBy('question_banks.id', $order);

        if (!empty($titleEn)) {
            $questionBankBuilder->where('question_banks.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $questionBankBuilder->where('question_banks.title', 'like', '%' . $title . '%');
        }

        if (!empty($subjectId)) {
            $questionBankBuilder->where('question_banks.subject_id', $subjectId);
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
     * @return QuestionBank
     */
    public function getOneQuestionBank(int $id): QuestionBank
    {
        /** @var QuestionBank|Builder $questionBankBuilder */
        $questionBankBuilder = QuestionBank::select([
            'question_banks.id',
            'question_banks.title',
            'question_banks.title_en',
            'question_banks.type',
            'question_banks.subject_id',
            'question_banks.option_1',
            'question_banks.option_1_en',
            'question_banks.option_2',
            'question_banks.option_2_en',
            'question_banks.option_3',
            'question_banks.option_3_en',
            'question_banks.option_4',
            'question_banks.option_4_en',
            'question_banks.difficulty_level',
            'question_banks.answer',
            'question_banks.row_status',
            'question_banks.created_at',
            'question_banks.updated_at',
            'question_banks.deleted_at',
        ]);

        if (is_numeric($id)) {
            $questionBankBuilder->where('question_banks.id', $id);
        }

        return $questionBankBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return QuestionBank
     */
    public function store(array $data): QuestionBank
    {
        $questionBank = app()->make(QuestionBank::class);
        $questionBank->fill($data);
        $questionBank->save();
        return $questionBank;
    }

    /**
     * @param QuestionBank $questionBank
     * @param array $data
     * @return QuestionBank
     */
    public function update(QuestionBank $questionBank, array $data): QuestionBank
    {
        $questionBank->fill($data);
        $questionBank->save();
        return $questionBank;
    }

    /**
     * @param QuestionBank $questionBank
     * @return bool
     */
    public function destroy(QuestionBank $questionBank): bool
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
            'title' => [
                'required',
                'string',
            ],
            'title_en' => [
                'nullable',
                'string'
            ],
            'type' => [
                'required',
                'int',
                Rule::in(QuestionBank::TYPES)
            ],
            'subject_id' => [
                'required',
                'int',
                'exists:subjects,id,deleted_at,NULL'
            ],
            'option_1' => [
                Rule::requiredIf(!empty($data['type'] && $data['type'] == QuestionBank::TYPE_MCQ)),
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
                Rule::requiredIf(!empty($data['type'] && $data['type'] == QuestionBank::TYPE_MCQ)),
                'required',
                'string',
                'max:600'
            ],
            'option_2_en' => [
                'nullable',
                'string',
                'max:300'
            ],
            'option_3' => [
                Rule::requiredIf(!empty($data['type'] && $data['type'] == QuestionBank::TYPE_MCQ)),
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
                Rule::requiredIf(!empty($data['type'] && $data['type'] == QuestionBank::TYPE_MCQ)),
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
                'required',
                'int',
                Rule::in(QuestionBank::ANSWERS)
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
