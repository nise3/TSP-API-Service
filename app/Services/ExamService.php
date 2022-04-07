<?php

namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Exam;
use App\Models\ExamQuestionBank;
use App\Models\ExamResult;
use App\Models\ExamSection;
use App\Models\ExamSectionQuestion;
use App\Models\ExamSet;
use App\Models\ExamType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Application;
use phpseclib3\Exception\NoSupportedAlgorithmsException;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function PHPUnit\Framework\throwException;


class ExamService
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
            'exam_types.subject_id',
            'exam_subjects.title  as exam_subject_title',
            'exam_subjects.title_en  as exam_subject_title_en',
            'exam_types.type',
            'exam_types.title',
            'exam_types.title_en',
            'exam_types.row_status',
            'exam_types.created_at',
            'exam_types.updated_at',
            'exam_types.deleted_at',
        ]);

        $examTypeBuilder->join("exam_subjects", function ($join) {
            $join->on('exam_types.subject_id', '=', 'exam_subjects.id')
                ->whereNull('exam_types.deleted_at');
        });

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
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
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


    public function getOneExamType(int $id)
    {
        /** @var ExamType|Builder $examTypeBuilder */
        $examTypeBuilder = ExamType::select([
            'exam_types.id',
            'exam_types.subject_id',
            'exam_subjects.title  as exam_subject_title',
            'exam_subjects.title_en  as exam_subject_title_en',
            'exam_types.type',
            'exam_types.title',
            'exam_types.title_en',
            'exam_types.row_status',
            'exam_types.created_at',
            'exam_types.updated_at',
            'exam_types.deleted_at',
        ]);

        $examTypeBuilder->join("exam_subjects", function ($join) {
            $join->on('exam_types.subject_id', '=', 'exam_subjects.id')
                ->whereNull('exam_types.deleted_at');
        });

        $examTypeBuilder->where('exam_types.id', $id);
        $examTypeBuilder->with('exams');
        /** @var Exam exam */
        return $examTypeBuilder->firstOrFail();
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
     * @return mixed
     */
    public function storeExam(array $data): mixed
    {
        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED) {
            $examIds = [];
            if (!empty($data['online'])) {
                $exam = app(Exam::class);
                $data['online']['type'] = Exam::EXAM_TYPE_ONLINE;
                $exam->fill($data['online']);
                $exam->save();
                $examIds['online'] = $exam->id;

            }
            if (!empty($data['offline'])) {
                $exam = app(Exam::class);
                $data['offline']['type'] = Exam::EXAM_TYPE_OFFLINE;
                $exam->fill($data['offline']);
                $exam->save();
                $examIds['offline'] = $exam->id;

            }

            return $examIds;

        } else {
            $exam = app(Exam::class);
            $exam->fill($data);
            $exam->save();
            return $exam;
        }

    }

    /**
     * @param array $data
     * @return array
     */
    public function storeExamSets(array $data): array
    {
        $setMapping = [];

        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED) {
            foreach ($data['offline']['sets'] as $examSetData) {
                $examSetData['uuid'] = ExamSet::examSetId();
                $examSetData['exam_id'] = $data['exam_ids']['offline'];
                $setMapping[$examSetData['id']] = $examSetData['uuid'];
                $examSet = app(ExamSet::class);
                $examSet->fill($examSetData);
                $examSet->save();
            }
        } else {
            foreach ($data['sets'] as $examSetData) {
                $examSetData['uuid'] = ExamSet::examSetId();
                $examSetData['exam_id'] = $data['exam_id'];
                $setMapping[$examSetData['id']] = $examSetData['uuid'];
                $examSet = app(ExamSet::class);
                $examSet->fill($examSetData);
                $examSet->save();
            }
        }

        return $setMapping;

    }

    /**
     * @param array $data
     * @throws Throwable
     */
    public function storeExamSections(array $data)
    {
        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED) {
            foreach ($data['online']['exam_questions'] as $examSectionData) {
                $examSectionData['uuid'] = ExamSection::examSectionId();
                $examSectionData['exam_id'] = $data['exam_ids']['online'];
                if (!empty($data['row_status'])) {
                    $examSectionData['row_status'] = $data['row_status'];
                }
                $examSection = app(ExamSection::class);
                $examSection->fill($examSectionData);
                $examSection->save();

                $examSectionQuestionData = $examSectionData['questions'];
                $examSectionData['exam_type'] = $data['type'];

                if ($examSectionData['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $this->storeExamSectionQuestions($examSectionData, $examSectionQuestionData);
                }
            }
            foreach ($data['offline']['exam_questions'] as $examSectionData) {
                $examSectionData['uuid'] = ExamSection::examSectionId();
                $examSectionData['exam_id'] = $data['exam_ids']['offline'];
                if (!empty($data['row_status'])) {
                    $examSectionData['row_status'] = $data['row_status'];
                }
                $examSection = app(ExamSection::class);
                $examSection->fill($examSectionData);
                $examSection->save();
                $examSectionData['exam_type'] = $data['type'];
                $examSectionData['subject_id'] = $data['subject_id'];

                if ($examSectionData['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $this->storeExamSectionQuestions($examSectionData);
                } else {
                    $examSectionQuestionData = $examSectionData['question_sets'];
                    $examSectionData['sets'] = $data['sets'];
                    $this->storeExamSectionQuestions($examSectionData, $examSectionQuestionData);
                }

            }
        } else {
            foreach ($data['exam_questions'] as $examSectionData) {
                $examSectionData['uuid'] = ExamSection::examSectionId();
                $examSectionData['exam_id'] = $data['exam_id'];
                if (!empty($data['row_status'])) {
                    $examSectionData['row_status'] = $data['row_status'];
                }
                $examSection = app(ExamSection::class);
                $examSection->fill($examSectionData);
                $examSection->save();


                $examSectionData['exam_type'] = $data['type'];
                $examSectionData['subject_id'] = $data['subject_id'];

                if ($data['type'] == Exam::EXAM_TYPE_ONLINE) {
                    $examSectionQuestionData = $examSectionData['questions'];
                    if ($examSectionData['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                        $this->storeExamSectionQuestions($examSectionData, $examSectionQuestionData);
                    }
                }
                if ($data['type'] == Exam::EXAM_TYPE_OFFLINE) {
                    $examSectionData['sets'] = $data['sets'];
                    if ($examSectionData['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                        $this->storeExamSectionQuestions($examSectionData);
                    } else {
                        $examSectionQuestionData = $examSectionData['question_sets'];
                        $this->storeExamSectionQuestions($examSectionData, $examSectionQuestionData);
                    }
                }


            }
        }


    }

    /**
     * @param array|null $examSectionQuestionData
     * @param array $examSectionData
     * @throws Throwable
     */
    public function storeExamSectionQuestions(array $examSectionData, array $examSectionQuestionData = null)
    {
        if ($examSectionData['exam_type'] == Exam::EXAM_TYPE_ONLINE) {
            foreach ($examSectionQuestionData as $examSectionQuestion) {
                $examSectionQuestion['uuid'] = ExamSectionQuestion::examSectionQuestionId();
                $examSectionQuestion['exam_id'] = $examSectionData['exam_id'];
                $examSectionQuestion['exam_section_uuid'] = $examSectionData['uuid'];
                $examSectionQuestion['question_selection_type'] = $examSectionData['question_selection_type'];
                $examSectionQuestion['question_id'] = $examSectionQuestion['id'];
                if (!empty($data['row_status'])) {
                    $examSectionQuestion['row_status'] = $data['row_status'];
                }
                $examQuestionSection = app(ExamSectionQuestion::class);
                $examQuestionSection->fill($examSectionQuestion);
                $examQuestionSection->save();
            }
        }
        if ($examSectionData['exam_type'] == Exam::EXAM_TYPE_OFFLINE) {
            {
                if ($examSectionData['question_selection_type'] = ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $questions = ExamQuestionBank::inRandomOrder()->limit($examSectionData['number_of_questions'])->get()->toArray();

                    throw_if(count($questions) != $examSectionData['number_of_questions'], ValidationException::withMessages([
                        "Number Of " . ExamQuestionBank::EXAM_QUESTION_VALIDATION_MESSAGES[$examSectionData["question_type"]] . " questions must be at least " . $examSectionData['number_of_questions'] . "[42001]"
                    ]));

                    foreach ($examSectionData['sets'] as $set) {
                        foreach ($questions as $question) {
                            $question['uuid'] = ExamSectionQuestion::examSectionQuestionId();
                            $question['exam_id'] = $examSectionData['exam_id'];
                            $question['exam_section_uuid'] = $examSectionData['uuid'];
                            $question['exam_set_uuid'] = $set;
                            $question['question_selection_type'] = $examSectionData['question_selection_type'];
                            $question['question_id'] = $question['id'];
                            if (!empty($data['row_status'])) {
                                $question['row_status'] = $data['row_status'];
                            }
                            $examQuestionSection = app(ExamSectionQuestion::class);
                            $examQuestionSection->fill($question);
                            $examQuestionSection->save();
                        }
                    }

                } else {
                    foreach ($examSectionQuestionData as $examSectionQuestionSet) {
                        foreach ($examSectionQuestionSet['questions'] as $examSectionQuestion) {
                            $examSectionQuestion['uuid'] = ExamSectionQuestion::examSectionQuestionId();
                            $examSectionQuestion['exam_id'] = $examSectionData['exam_id'];
                            $examSectionQuestion['exam_section_uuid'] = $examSectionData['uuid'];
                            $examSectionQuestion['exam_set_uuid'] = $examSectionData['set'][$examSectionQuestionSet['id']];
                            $examSectionQuestion['question_selection_type'] = $examSectionData['question_selection_type'];
                            $examSectionQuestion['question_id'] = $examSectionQuestion['id'];
                            if (!empty($data['row_status'])) {
                                $examSectionQuestion['row_status'] = $data['row_status'];
                            }
                            $examQuestionSection = app(ExamSectionQuestion::class);
                            $examQuestionSection->fill($examSectionQuestion);
                            $examQuestionSection->save();
                        }

                    }
                }

            }

        }
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
     * @param ExamType $ExamType
     */
    public function destroy(ExamType $ExamType)
    {

        $ExamTypeId = $ExamType->id;
        $ExamType->delete();
        $examsIds = Exam::where('exam_type_id', $ExamTypeId)->pluck('id')->toArray();
        Exam::whereIn('id', $examsIds)->delete();
        ExamSection::whereIn('exam_id', $examsIds)->delete();
        ExamSet::whereIn('exam_id', $examsIds)->delete();
        ExamSectionQuestion::whereIn('exam_id', $examsIds)->delete();


    }

    public function getExamYouthList(Exam $Exam)
    {
        $ExamId = $Exam->id;

        $youthIds = ExamResult::where('exam_id', $ExamId)->pluck('youth_id')->unique()->toArray();
        $youthProfiles=null;
        if ($youthIds) {
            $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
        }
        return $youthProfiles->toArray();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        if (!empty($data['purpose_name']) && $data['purpose_name'] == ExamType::EXAM_PURPOSE_BATCH) {
            $examPurposeTable = ExamType::EXAM_PURPOSE_TABLE_BATCH;
        }

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
                'exists:' . $examPurposeTable . ',id,deleted_at,NULL',
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
                'date_format:Y-m-d',

            ];
            $rules['online.duration'] = [
                'required',
                'int'
            ];

            $rules['online.venue'] = [
                'nullable',
                'string',
            ];
            $rules['online.total_marks'] = [
                'nullable',
                'numeric',
            ];
            $rules['online.exam_questions'] = [
                'array',
                'required',
                'size:5',
            ];
            $rules['online.exam_questions.*'] = [
                'array',
                'required',
            ];
            $rules['online.exam_questions.*.question_type'] = [
                'integer',
                'required',
                Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
            ];
            $rules['online.exam_questions.*.number_of_questions'] = [
                'integer',
                'required',
            ];
            $rules['online.exam_questions.*.total_marks'] = [
                'numeric',
                'required',
            ];
            $rules['online.exam_questions.*.question_selection_type'] = [
                'integer',
                'required',
                Rule::in(ExamQuestionBank::QUESTION_SELECTION_TYPES)
            ];
            $rules['offline'] = [
                'array',
                'required'
            ];
            $rules['offline.exam_date'] = [
                'required',
                'date_format:Y-m-d',
            ];
            $rules['offline.duration'] = [
                'required',
                'int'
            ];
            $rules['offline.venue'] = [
                'nullable',
                'string',
            ];
            $rules['offline.total_marks'] = [
                'required',
                'numeric',
            ];
            $rules["offline.sets"] = [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED;
                }),
                "nullable",
                "array",
            ];
            $rules["offline.sets.*.id"] = [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED;
                }),
                'nullable',
                'string',
                "distinct",
            ];
            $rules["offline.sets.*.title"] = [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_MIXED;
                }),
                'string',
                'nullable',
            ];
            $rules["offline.sets.*.title_en"] = [
                "nullable",
                'string',
            ];

            $rules['offline.exam_questions.*.question_sets'] = [
                'required',
                'array',
            ];
            $rules['offline.exam_questions.*.question_sets.*'] = [
                'required',
                'array',
            ];
            $rules['offline.exam_questions.*.question_sets.*.id'] = [
                'string',
                'required',
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions'] = [
                'array',
                'required',
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*'] = [
                'array',
                'required',
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.id'] = [
                'required',
                'integer',
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.title_en'] = [
                'nullable',
                'string',
            ];

            $rules['offline.exam_questions.*.question_sets.*.questions.*.accessor_type'] = [
                'required',
                'string',
                'max:100',
                Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.accessor_id'] = [
                'required',
                'int',
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.subject_id'] = [
                'required',
                'int',
                'exists:exam_subjects,id,deleted_at,NULL'
            ];

            $rules['offline.exam_questions.*.question_sets.*.questions.*.question_type'] = [
                'required',
                'int',
                Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
            ];

            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_1'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && $data['exam_questions']['question_sets']['questions']['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_1_en'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && $data['exam_questions']['question_sets']['questions']['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'string',
                'max:300'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_2'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && $data['exam_questions']['question_sets']['questions']['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_2_en'] = [
                'nullable',
                'string',
                'max:300'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_3'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && $data['exam_questions']['question_sets']['questions']['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_3_en'] = [
                'nullable',
                'string',
                'max:300'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_4'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && $data['exam_questions']['question_sets']['questions']['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                'nullable',
                'string',
                'max:600'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.option_4_en'] = [
                'nullable',
                'string',
                'max:300'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.answers'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && array_key_exists($data['exam_questions']['question_sets']['questions']['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                'nullable',
                'array',
                'min:1'
            ];
            $rules['offline.exam_questions.*.question_sets.*.questions.*.answers.*'] = [
                Rule::requiredIf(!empty($data['exam_questions']['question_sets']['questions']['question_type']) && array_key_exists($data['exam_questions']['question_sets']['questions']['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                'nullable',
                'string',
            ];

        } else {
            $rules['exam_date'] = [
                'required',
                'date_format:Y-m-d',
            ];
            $rules['duration'] = [
                'required',
                'int'
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
            $rules["sets"] = [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
                }),
                "nullable",
                "array",
            ];
            $rules["sets.*.id"] = [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
                }),
                'nullable',
                'string',
                "distinct",
            ];
            $rules["sets.*.title"] = [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
                }),
                'string',
                'nullable',
            ];
            $rules["sets.*.title_en"] = [
                "nullable",
                'string',
            ];
            $rules['exam_questions'] = [
                'array',
                'required',
            ];
            $rules['exam_questions.*'] = [
                'array',
                'required',
            ];
            $rules['exam_questions.*.question_type'] = [
                'integer',
                'required',
                Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
            ];
            $rules['exam_questions.*.number_of_questions'] = [
                'integer',
                'required',
            ];
            $rules['exam_questions.*.total_marks'] = [
                'numeric',
                'required',
            ];
            $rules['exam_questions.*.question_selection_type'] = [
                'integer',
                'required',
                Rule::in(ExamQuestionBank::QUESTION_SELECTION_TYPES)
            ];
        }

        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_ONLINE) {
            $index = 0;
            foreach ($data['exam_questions'] as $examQuestion) {
                if ($examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_FIXED) {
                    $onlineExamQuestionNumbers = $examQuestion['number_of_questions'];
                } else if ($examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $onlineExamQuestionNumbers = $examQuestion['number_of_questions'] + 1;
                }
                if ($examQuestion['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $rules['exam_questions.' . $index . '.questions'] = [
                        'array',
                        'nullable',
                        'size:' . $onlineExamQuestionNumbers
                    ];
                    $rules['exam_questions.' . $index . '.questions.*'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'nullable',
                        'array',
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.id'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'integer',
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.title'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'string',
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.title_en'] = [
                        'nullable',
                        'string',
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.accessor_type'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'string',
                        'max:100',
                        Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.accessor_id'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'int',
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.subject_id'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'int',
                        'exists:exam_subjects,id,deleted_at,NULL'
                    ];

                    $rules['exam_questions.' . $index . '.questions.*.question_type'] = [
                        Rule::requiredIf(!empty($examQuestion['questions'])),
                        'int',
                        Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
                    ];

                    $rules['exam_questions.' . $index . '.questions.*.option_1'] = [
                        Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                        'nullable',
                        'string',
                        'max:600'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_1_en'] = [
                        'nullable',
                        'string',
                        'max:300'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_2'] = [
                        Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                        'nullable',
                        'string',
                        'max:600'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_2_en'] = [
                        'nullable',
                        'string',
                        'max:300'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_3'] = [
                        Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                        'nullable',
                        'string',
                        'max:600'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_3_en'] = [
                        'nullable',
                        'string',
                        'max:300'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_4'] = [
                        Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                        'nullable',
                        'string',
                        'max:600'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.option_4'] = [
                        'nullable',
                        'string',
                        'max:300'
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.answers'] = [
                        Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                        'nullable',
                        'array',
                    ];
                    $rules['exam_questions.' . $index . '.questions.*.answers'] = [
                        'nullable',
                        'string',
                    ];


                }

                $index++;
            }
        }

        if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE) {
            foreach ($data['exam_questions'] as $examQuestion) {
                if ($examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_FIXED) {
                    $offlineExamQuestionNumbers = $examQuestion['number_of_questions'];
                } else if ($examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $offlineExamQuestionNumbers = $examQuestion['number_of_questions'] + 1;
                } else {
                    $offlineExamQuestionNumbers = 0;
                }
                if ($examQuestion['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                    $rules['exam_questions.*.question_sets'] = [
                        'required',
                        'array',
                    ];
                    $rules['exam_questions.*.question_sets.*'] = [
                        'required',
                        'array',

                    ];
                    if (!empty($examQuestion['question_sets'])) {
                        $index = 0;
                        foreach ($examQuestion['question_sets'] as $examQuestionSet) {

                            $rules['exam_questions.*.question_sets.' . $index . '.id'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'string',
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'array',
                                'size:' . $offlineExamQuestionNumbers
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'array',

                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.id'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'integer',
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.title_en'] = [
                                'nullable',
                                'string',
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.title'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'string',
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.individual_marks'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'numeric',
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.accessor_type'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'string',
                                'max:100',
                                Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.accessor_id'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'int',
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.subject_id'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'int',
                                'exists:exam_subjects,id,deleted_at,NULL'
                            ];

                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.question_type'] = [
                                Rule::requiredIf(!empty($examQuestionSet)),
                                'nullable',
                                'int',
                                Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
                            ];

                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_1'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                                'nullable',
                                'string',
                                'max:600'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_1_en'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                                'nullable',
                                'string',
                                'max:300'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_2'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                                'nullable',
                                'string',
                                'max:600'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_2_en'] = [
                                'nullable',
                                'string',
                                'max:300'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_3'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                                'nullable',
                                'string',
                                'max:600'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_3_en'] = [
                                'nullable',
                                'string',
                                'max:300'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_4'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                                'nullable',
                                'string',
                                'max:600'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.option_4'] = [
                                'nullable',
                                'string',
                                'max:300'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.answers'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && array_key_exists($examQuestion['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                                'nullable',
                                'array',
                                'min:1'
                            ];
                            $rules['exam_questions.*.question_sets.' . $index . '.questions.*.answers.*'] = [
                                Rule::requiredIf(!empty($examQuestionSet) && array_key_exists($examQuestion['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                                'nullable',
                                'string',
                            ];

                            $index++;
                        }

                    }

                }
            }

        }

        return Validator::make($data, $rules, $customMessage);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */

    public
    function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($request->all(), $rules, $customMessage);
    }
}

