<?php
namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Exam;
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

class ExamService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $examTypeId=$request['exam_type_id'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Exam|Builder $examBuilder */
        $examBuilder = Exam::select([
            'exams.id',
            'exams.exam_type_id',
            'exams.exam_date',
            'exams.start_time',
            'exams.end_time',
            'exams.venue',
            'exams.total_marks',
            'exams.row_status',
            'exams.created_at',
            'exams.updated_at',
            'exams.deleted_at',
        ]);

        $examBuilder->orderBy('exams.id', $order);

        if (is_numeric($rowStatus)) {
            $examBuilder->where('exams.row_status', $rowStatus);
        }
        if (!empty($examTypeId)) {
            $examBuilder->where('exams.subjectId', 'like', '%' . $examTypeId . '%');
        }
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $Exam = $examBuilder->paginate($pageSize);
            $paginateData = (object)$Exam->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $Exam = $examBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $Exam->toArray()['data'] ?? $Exam->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return Exam
     */
    public function getOneExam(int $id): Exam
    {
        /** @var Exam|Builder $examBuilder */
        $examBuilder = Exam::select([
            'exams.id',
            'exams.exam_type_id',
            'exams.exam_date',
            'exams.start_time',
            'exams.end_time',
            'exams.venue',
            'exams.total_marks',
            'exams.row_status',
            'exams.created_at',
            'exams.updated_at',
            'exams.deleted_at',
        ]);
        $examBuilder->where('exams.id', $id);
        /** @var Exam exam */
        return $examBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Exam
     * @throws Throwable
     */
    public function store(array $data): Exam
    {
        $exam = app()->make(Exam::class);
        $exam->fill($data);
        $exam->save();
        return $exam;
    }

    /**
     * @param Exam $Exam
     * @param array $data
     * @return Exam
     */
    public function update(Exam $Exam, array $data): Exam
    {
        $Exam->fill($data);
        $Exam->save();
        return $Exam;
    }

    /**
     * @param Exam $Exam
     * @return bool
     */
    public function destroy(Exam $Exam): bool
    {
        return $Exam->delete();
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
            "sets" => [
                Rule::requiredIf(function () use ($data) {
                    if ($data['type'] === Exam::EXAM_TYPE_OFFLINE) {
                        return true;
                    }
                }),
                "nullable",
                "array",
                "min:1"
            ],
            "sets.*.id" => [
                "required",
                'string',
                "distinct",
                "min:1"
            ],
            "sets.*.title" => [
                "required",
                'string',
                "distinct",
                "min:1"
            ],
            "sets.*.title_en" => [
                "required",
                'string',
                "min:1"
            ],
            "exam_question" => [
                "required",
                "array",
                "min:1",
                "max:10"
            ],
            "exam_question.*" => [
                "required",
                "array",
                "min:1",
                "max:10"
            ],

            "exam_question.*.mcq" => [
                "required",
                'array',
                "min:1"
            ],

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
                Rule::in(ExamType::EXAM_PURPOSES)
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
            'exam_type_id'=>[
                'required',
                'int',
                'min:1'
            ],
            'exam_date' => [
                'required',
                'date'
            ],
            'start_time' => [
                'nullable',
                'date_format:H:i:s'
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i:s'
            ],
            'venue' => [
                'nullable',
                'string',
                'max:500'
            ],
            'total_marks' => [
                'nullable',
                'string',
                'max:500'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];

        if($data['exam_question']){
            foreach ($data['exam_question'] as $examQuestion ){

            }
        }


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

