<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Exam;
use App\Models\ExamQuestionBank;
use App\Models\ExamSet;
use App\Models\ExamType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;


class ExamService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $examTypeId = $request['exam_type_id'] ?? "";
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
     * @return ExamType
     */
    public function storeExamType(array $data): ExamType
    {
        $examType = app(ExamType::class);
        $examType->fill($data);
        $examType->save();
        return $examType;
    }

    /**
     * @param array $data
     * @return Exam
     */
    public function storeExam(array $data): Exam
    {
        $exam = app(Exam::class);
        $exam->fill($data);
        $exam->save();
        return $exam;
    }

    /**
     * @param array $data
     * @return array
     */
    public function storeExamSets(array $data): array
    {
        $setMapping = [];
        foreach ($data['sets'] as $examSetData) {
            $examSetData['uuid'] = ExamSet::examSetId();
            $examSetData['exam_id'] = $data['exam_id'];
            $setMapping[$examSetData['id']] = $examSetData['uuid'];
            $examSet = app(ExamSet::class);
            $examSet->fill($examSetData);
            $examSet->save();
        }
        return $setMapping;

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
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Order must be either ASC or DESC. [30000]',
        ];
        $rules = [
            'type' => [
                'required',
                'string',
                'max:500',
                Rule::in(Exam::EXAM_TYPES)
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
            'subject_id' => [
                'required',
                'int',
                'exists:exam_subjects,id,deleted_at,NULL'
            ],
            'purpose_name' => [
                'required',
                'string',
                'max:500',
                Rule::in(ExamType::EXAM_PURPOSES)
            ],
            'purpose_id' => [
                'required',
                'int',
            ],
            "sets" => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
                }),
                "nullable",
                "array",
            ],
            "sets.*.id" => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
                }),
                'nullable',
                'string',
                "distinct",
            ],
            "sets.*.title" => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
                }),
                'string',
                'nullable',
            ],
            "sets.*.title_en" => [
                "nullable",
                'string',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];

        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED) {
            $rules['online'] = [
                'array',
                'required'
            ];
            $rules['online.exam_date'] = [
                'required',
                'date'
            ];
            $rules['online.start_time'] = [
                'nullable',
                'date_format:H:i:s'
            ];
            $rules['online.end_time'] = [
                'nullable',
                'date_format:H:i:s'
            ];
            $rules['online.venue'] = [
                'nullable',
                'string',
            ];
            $rules['online.total_marks'] = [
                'nullable',
                'numeric',
            ];
            $rules['offline'] = [
                'array',
                'required'
            ];
            $rules['offline.exam_date'] = [
                'required',
                'date'
            ];
            $rules['offline.start_time'] = [
                'nullable',
                'date_format:H:i:s'
            ];
            $rules['offline.end_time'] = [
                'nullable',
                'date_format:H:i:s'
            ];
            $rules['offline.venue'] = [
                'nullable',
                'string',
            ];
            $rules['offline.total_marks'] = [
                'required',
                'numeric',
            ];

        } else {
            $rules['exam_date'] = [
                'required',
                'date'
            ];
            $rules['start_time'] = [
                'nullable',
                'date_format:H:i:s'
            ];
            $rules['end_time'] = [
                'nullable',
                'date_format:H:i:s'
            ];
            $rules['venue'] = [
                'nullable',
                'string',
            ];
            $rules['total_marks'] = [
                'required',
                'numeric',
            ];
        }

        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_ONLINE) {


        }

        return Validator::make($data, $rules, $customMessage);
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

