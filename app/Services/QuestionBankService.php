<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\RplQuestionBank;
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

        /** @var RplQuestionBank|Builder $questionBankBuilder */
        $questionBankBuilder = RplQuestionBank::select([
            'rpl_question_banks.id',
            'rpl_question_banks.title',
            'rpl_question_banks.title_en',
            'rpl_question_banks.type',
            'rpl_question_banks.subject_id',
            'subjects.title as subject_title',
            'subjects.title_en as subject_title_en',
            'rpl_question_banks.option_1',
            'rpl_question_banks.option_1_en',
            'rpl_question_banks.option_2',
            'rpl_question_banks.option_2_en',
            'rpl_question_banks.option_3',
            'rpl_question_banks.option_3_en',
            'rpl_question_banks.option_4',
            'rpl_question_banks.option_4_en',
            'rpl_question_banks.difficulty_level',
            'rpl_question_banks.answer',
            'rpl_question_banks.row_status',
            'rpl_question_banks.created_at',
            'rpl_question_banks.updated_at',
            'rpl_question_banks.deleted_at',
        ]);

        if (!$isPublicApi) {
            $questionBankBuilder->acl();
        }

        $questionBankBuilder->orderBy('rpl_question_banks.id', $order);

        $questionBankBuilder->join('subjects', function ($join) {
            $join->on('rpl_question_banks.subject_id', '=', 'subjects.id')
                ->whereNull('subjects.deleted_at');
        });

        if (!empty($titleEn)) {
            $questionBankBuilder->where('rpl_question_banks.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $questionBankBuilder->where('rpl_question_banks.title', 'like', '%' . $title . '%');
        }

        if (!empty($subjectId)) {
            $questionBankBuilder->where('rpl_question_banks.subject_id', $subjectId);
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
     * @return RplQuestionBank
     */
    public function getOneQuestionBank(int $id): RplQuestionBank
    {
        /** @var RplQuestionBank|Builder $questionBankBuilder */
        $questionBankBuilder = RplQuestionBank::select([
            'rpl_question_banks.id',
            'rpl_question_banks.title',
            'rpl_question_banks.title_en',
            'rpl_question_banks.type',
            'rpl_question_banks.subject_id',
            'subjects.title as subject_title',
            'subjects.title_en as subject_title_en',
            'rpl_question_banks.option_1',
            'rpl_question_banks.option_1_en',
            'rpl_question_banks.option_2',
            'rpl_question_banks.option_2_en',
            'rpl_question_banks.option_3',
            'rpl_question_banks.option_3_en',
            'rpl_question_banks.option_4',
            'rpl_question_banks.option_4_en',
            'rpl_question_banks.difficulty_level',
            'rpl_question_banks.answer',
            'rpl_question_banks.row_status',
            'rpl_question_banks.created_at',
            'rpl_question_banks.updated_at',
            'rpl_question_banks.deleted_at',
        ]);
        $questionBankBuilder->join('subjects', function ($join) {
            $join->on('rpl_question_banks.subject_id', '=', 'subjects.id')
                ->whereNull('subjects.deleted_at');
        });

        $questionBankBuilder->where('rpl_question_banks.id', $id);

        return $questionBankBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplQuestionBank
     */
    public function store(array $data): RplQuestionBank
    {
        $questionBank = app()->make(RplQuestionBank::class);
        $questionBank->fill($data);
        $questionBank->save();
        return $questionBank;
    }

    /**
     * @param RplQuestionBank $questionBank
     * @param array $data
     * @return RplQuestionBank
     */
    public function update(RplQuestionBank $questionBank, array $data): RplQuestionBank
    {
        $questionBank->fill($data);
        $questionBank->save();
        return $questionBank;
    }

    /**
     * @param RplQuestionBank $questionBank
     * @return bool
     */
    public function destroy(RplQuestionBank $questionBank): bool
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
                Rule::in(RplQuestionBank::TYPES)
            ],
            'subject_id' => [
                'required',
                'int',
                'exists:subjects,id,deleted_at,NULL'
            ],
            'option_1' => [
                Rule::requiredIf(!empty($data['type'] && $data['type'] == RplQuestionBank::TYPE_MCQ)),
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
                Rule::requiredIf(!empty($data['type'] && $data['type'] == RplQuestionBank::TYPE_MCQ)),
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
                Rule::requiredIf(!empty($data['type'] && $data['type'] == RplQuestionBank::TYPE_MCQ)),
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
                Rule::requiredIf(!empty($data['type'] && $data['type'] == RplQuestionBank::TYPE_MCQ)),
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
                Rule::in(RplQuestionBank::ANSWERS)
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
