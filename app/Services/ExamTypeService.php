<?php
namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\ExamType;
use App\Models\RplSubject;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExamTypeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {

        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $subjectId = $request['subject_id'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var ExamType|Builder $examTypeBuilder */
        $examTypeBuilder = ExamType::select([
            'exam_types.id',
            'exam_types.type',
            'exam_types.title',
            'exam_types.title_en',
            'exam_types.subject_id',
            'exam_types.accessor_type',
            'exam_types.accessor_id',
            'exam_types.purpose_name',
            'exam_types.purpose_id',
            'exam_types.row_status',
            'exam_types.created_at',
            'exam_types.updated_at',
            'exam_types.deleted_at',
        ]);

        $examTypeBuilder->orderBy('exam_types.id', $order);

        if (is_numeric($rowStatus)) {
            $examTypeBuilder->where('exam_types.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $examTypeBuilder->where('exam_types.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $examTypeBuilder->where('exam_types.title', 'like', '%' . $title . '%');
        }

        if (!empty($subjectId)) {
            $examTypeBuilder->where('exam_types.subjectId', 'like', '%' . $subjectId . '%');
        }

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $ExamType = $examTypeBuilder->paginate($pageSize);
            $paginateData = (object)$ExamType->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ExamType = $examTypeBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ExamType->toArray()['data'] ?? $ExamType->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return ExamType
     */
    public function getOneExamType(int $id): ExamType
    {
        /** @var ExamType|Builder $examTypeBuilder */
        $examTypeBuilder = ExamType::select([
            "exam_types.id",
            'exam_types.type',
            'exam_types.title',
            'exam_types.title_en',
            'exam_types.subject_id',
            'exam_types.accessor_type',
            'exam_types.accessor_id',
            'exam_types.purpose_name',
            'exam_types.purpose_id',
            'exam_types.row_status',
            'exam_types.created_at',
            'exam_types.updated_at',
            'exam_types.deleted_at',
        ]);
        $examTypeBuilder->where('exam_types.id', $id);
        /** @var ExamType exam */
        return $examTypeBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return ExamType
     * @throws Throwable
     */
    public function store(array $data): ExamType
    {
        $exam = app()->make(ExamType::class);
        $exam->fill($data);
        $exam->save();
        return $exam;
    }

    /**
     * @param ExamType $ExamType
     * @param array $data
     * @return ExamType
     */
    public function update(ExamType $ExamType, array $data): ExamType
    {
        $ExamType->fill($data);
        $ExamType->save();
        return $ExamType;
    }

    /**
     * @param ExamType $ExamType
     * @return bool
     */
    public function destroy(ExamType $ExamType): bool
    {
        return $ExamType->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Order must be either ASC or DESC. [30000]',
        ];
        $rules = [
            'title' => [
                'required',
                'string',
                'max:500'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'type' => [
                'required',
                'string',
                'max:500',
                Rule::in(Exam::EXAM_TYPES)
            ],
            'subject_id' => [
                'required',
                'int',
                'min:1'
            ],
            'purpose_id' => [
                'required',
                'int',
                'min:1'
            ],
            'purpose_name' => [
                'required',
                'string',
                'max:500',
                Rule::in(Exam::EXAM_PURPOSES)
            ],
            'accessor_type' => [
                'required',
                'string',
                'max:250',
//                Rule::in(ExamType::EXAM_SUBJECT_ASSESSOR_TYPES)
            ],
            'accessor_id' => [
                'required',
                'int',
                'min:1'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
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
        $rules = [

            'subject_id' => 'nullable|int|gt:0',
            'title_en' => 'nullable|max:250',
            'title' => 'nullable|max:500|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }
}

