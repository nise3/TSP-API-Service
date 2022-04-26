<?php

namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamQuestionBank;
use App\Models\ExamResult;
use App\Models\ExamSection;
use App\Models\ExamSectionQuestion;
use App\Models\ExamSet;
use App\Models\ExamType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
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
            $examTypeBuilder->where('exam_types.subject_id', 'like', '%' . $subjectId . '%');
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


    /**
     * @param array $data
     */
    public function submitExamQuestionPaper(array $data)
    {
        $examSections = ExamSection::where('exam_id', $data['exam_id'])->get()->toArray();

        $examSectionIdsByQuestionType = [];

        foreach ($examSections as $examSection) {
            $examSectionIdsByQuestionType[$examSection['question_type']] = $examSection['uuid'];
        }
        foreach ($data['questions'] as $questionData) {
            if (empty($questionData['exam_section_question_id'])) {
                $question = ExamQuestionBank::findOrFail($questionData['question_id'])->toArray();

                $question['exam_section_uuid'] = $examSectionIdsByQuestionType[$question['question_type']];
                $question['question_id'] = $question['id'];
                /** question  id is assigned to question_id **/
                unset($question['id']);
                $question['question_selection_type'] = ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK;
                $question['uuid'] = ExamSectionQuestion::examSectionQuestionId();
                $question['individual_marks'] = $questionData['individual_marks'];

                $examSectionQuestion = $this->storeRandomQuestionsToExamSectionQuestions($question);
            }
            $question['youth_id'] = $data['youth_id'];
            $question['exam_id'] = $data['exam_id'];
            $question['exam_section_question_id'] = $examSectionQuestion->uuid ?? $questionData['exam_section_question_id'];

            $examSectionQuestionInfo = ExamSectionQuestion::where('uuid', $question['exam_section_question_id'])->first()->toArray();
            if (in_array($examSectionQuestionInfo['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)) {
                $isCorrectAnswer = $this->getAutoCalculatedAchievedMarks($examSectionQuestionInfo, $questionData);
                $isCorrectAnswer ? $question['marks_achieved'] = $questionData['individual_marks'] : $question['marks_achieved'] = floatval(0);
            }
            $examResult = app(ExamResult::class);
            $examResult->fill($question);
            $examResult->save();
        }

    }


    /**
     * @param array $examSectionQuestion
     * @param array $questionData
     * @return bool
     */
    private function getAutoCalculatedAchievedMarks(array $examSectionQuestion, array $questionData): bool
    {
        $answerStatus = false;
        $givenAnswer = $questionData['answers'];
        $correctAnswer = $examSectionQuestion['answers'];

        for ($i = 0; $i < count($correctAnswer); $i++) {

            if (!is_numeric($correctAnswer[$i])) {
                $correctAnswer[$i] = strtolower($correctAnswer[$i]);
                $givenAnswer[$i] = strtolower($givenAnswer[$i]);
            }
            if ($correctAnswer[$i] == $givenAnswer[$i]) {
                $answerStatus = true;
            } else {
                $answerStatus = false;
                break;
            }
        }

        return $answerStatus;


    }


    /**
     * @param array $question
     * @return ExamSectionQuestion
     */
    private function storeRandomQuestionsToExamSectionQuestions(array $question): ExamSectionQuestion
    {
        $examSectionQuestion = app(ExamSectionQuestion::class);
        $examSectionQuestion->fill($question);
        $examSectionQuestion->save();

        return $examSectionQuestion;

    }

    /**
     * @param array $request
     * @param int $id
     * @return Model|Builder
     */
    public function getOneExamType(array $request, int $id): Model|Builder
    {
        $purposeName = $request['purpose_name'] ?? '';
        /** @var ExamType|Builder $examTypeBuilder */
        $examTypeBuilder = ExamType::select([
            'exam_types.id',
            'exam_types.subject_id',
            'exam_subjects.title  as exam_subject_title',
            'exam_subjects.title_en  as exam_subject_title_en',
            'exam_types.type',
            'exam_types.purpose_id',
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

        if ($purposeName == ExamType::EXAM_PURPOSE_BATCH) {
            $examTypeBuilder->join("batches", function ($join) {
                $join->on('exam_types.purpose_id', '=', 'batches.id')
                    ->whereNull('batches.deleted_at');
            });
            $examTypeBuilder->join("training_centers", function ($join) {
                $join->on('batches.training_center_id', '=', 'training_centers.id')
                    ->whereNull('training_centers.deleted_at');
            });
            $examTypeBuilder->join("courses", function ($join) {
                $join->on('batches.course_id', '=', 'courses.id')
                    ->whereNull('courses.deleted_at');
            });
            $examTypeBuilder->addSelect('batches.title as batch_title')
                ->addSelect('batches.title_en as batch_title_en')
                ->addSelect('training_center_id')
                ->addSelect('training_centers.title as training_center_title')
                ->addSelect('training_centers.title_en as training_center_title_en')
                ->addSelect('course_id')
                ->addSelect('courses.title as course_title')
                ->addSelect('courses.title_en as course_title_en');
        }


        $examTypeBuilder->where('exam_types.id', $id);
        $examTypeBuilder->with('exams.examSets');
        $examTypeBuilder->with('exams.examSections.questions');
        /** @var Exam exam */
        return $examTypeBuilder->firstOrFail();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getExamQuestionPaper(int $id): array
    {
        /** @var Builder $examQuestionPaperBuilder */
        $examQuestionPaperBuilder = Exam::select([
            'exams.id',
            'exam_types.title',
            'exam_types.title_en',
            'exam_types.subject_id',
            'exam_types.purpose_name',
            'exam_types.purpose_id',
            'exam_subjects.title as subject_title',
            'exam_subjects.title_en as subject_title_en',
            'exams.exam_date',
            'exams.duration',
            'exams.venue',
            'exams.total_marks',
        ]);

        $examQuestionPaperBuilder->join("exam_types", function ($join) {
            $join->on('exams.exam_type_id', '=', 'exam_types.id')
                ->whereNull('exam_types.deleted_at');
        });

        $examQuestionPaperBuilder->join("exam_subjects", function ($join) {
            $join->on('exam_types.subject_id', '=', 'exam_subjects.id')
                ->whereNull('exam_types.deleted_at');
        });


        $examQuestionPaperBuilder->where('exams.id', $id);

        $exam = $examQuestionPaperBuilder->firstOrFail()->toArray();
        $exam['exam_sections'] = $this->getExamSectionByExam($id);

        if ($exam['purpose_name'] == ExamType::EXAM_PURPOSE_BATCH) {
            /** @var Batch $batch */
            $batch = Batch::findOrFail($exam['purpose_id']);
            $exam['course_id'] = $batch->course_id;
        }
        foreach ($exam['exam_sections'] as &$examSection) {
            $examSection['subject_id'] = $exam['subject_id'];
            if ($examSection['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                $examSection['questions'] = $this->getRandomExamSectionQuestionBySection($examSection);
            } else {
                $examSection['questions'] = $this->getExamSectionQuestionBySection($examSection);
            }
            /** remove answer from tittle in fill in the blanks questions */
            foreach ($examSection['questions'] as &$question) {
                if ($examSection["question_type"] == ExamQuestionBank::EXAM_QUESTION_TYPE_Fill_IN_THE_BLANKS) {
                    preg_match_all('/\[{2}(.*?)\]{2}/is', $question['title'], $match);
                    $question['title'] = preg_replace('/\[{2}(.*?)\]{2}/is', '[[]]', $question['title']);
                }
            }
        }

        return $exam;

    }

    /**
     * @param int $examId
     * @return array
     */
    private function getExamSectionByExam(int $examId): array
    {
        return ExamSection::select([
            'exam_sections.uuid',
            'exam_sections.question_type',
            'exam_sections.total_marks',
            'exam_sections.exam_id',
            'exam_sections.question_selection_type',
            'exam_sections.number_of_questions',
        ])->where('exam_sections.exam_id', $examId)->get()->toArray();
    }


    /**
     * @param $examSection
     * @return mixed
     */
    private function getRandomExamSectionQuestionBySection($examSection): mixed
    {
        /** @var Builder $examQuestionBuilder */
        $examQuestionBuilder = ExamQuestionBank::select([
            'exam_question_banks.id as question_id',
            'exam_question_banks.title',
            'exam_question_banks.title_en',
            'exam_question_banks.subject_id',
            'exam_question_banks.accessor_type',
            'exam_question_banks.accessor_id',
            'exam_question_banks.question_type',
            'exam_question_banks.option_1',
            'exam_question_banks.option_1_en',
            'exam_question_banks.option_2',
            'exam_question_banks.option_2_en',
            'exam_question_banks.option_3',
            'exam_question_banks.option_3_en',
            'exam_question_banks.option_4',
            'exam_question_banks.option_4_en',
        ]);
        $examQuestionBuilder->where('question_type', $examSection['question_type']);
        $examQuestionBuilder->where('subject_id', $examSection['subject_id']);
        $examQuestionBuilder->inRandomOrder();
        $examQuestionBuilder->limit($examSection['number_of_questions']);

        $examQuestions = $examQuestionBuilder->get()->toArray();

        foreach ($examQuestions as &$examQuestion) {
            $examQuestion['individual_marks'] = $examSection['total_marks'] / floatval($examSection['number_of_questions']);
        }

        return $examQuestions;
    }


    /**
     * @param $examSection
     * @return array
     */
    private function getExamSectionQuestionBySection($examSection): array
    {
        /** @var Builder $examSectionBuilder */
        $examSectionBuilder = ExamSectionQuestion::select([
            'exam_section_questions.uuid as exam_section_question_id',
            'exam_section_questions.question_id',
            'exam_section_questions.individual_marks',
            'exam_section_questions.exam_id',
            'exam_section_questions.title',
            'exam_section_questions.title_en',
            'exam_section_questions.subject_id',
            'exam_section_questions.question_type',
            'exam_section_questions.accessor_type',
            'exam_section_questions.accessor_id',
            'exam_section_questions.option_1',
            'exam_section_questions.option_1_en',
            'exam_section_questions.option_2',
            'exam_section_questions.option_2_en',
            'exam_section_questions.option_3',
            'exam_section_questions.option_3_en',
            'exam_section_questions.option_4',
            'exam_section_questions.option_4_en',
        ]);
        $examSectionBuilder->where('exam_section_questions.exam_section_uuid', $examSection['uuid']);

        return $examSectionBuilder->get()->toArray();
    }

    /**
     * @param $examSection
     * @return array
     */
    private function getExamSectionQuestionWithAnswerBySection($examSection): array
    {
        /** @var Builder $examSectionBuilder */
        $examSectionBuilder = ExamSectionQuestion::select([
            'exam_section_questions.question_id',
            'exam_section_questions.exam_section_uuid',
            'exam_section_questions.individual_marks',
            'exam_section_questions.exam_id',
            'exam_section_questions.title',
            'exam_section_questions.title_en',
            'exam_section_questions.subject_id',
            'exam_section_questions.question_type',
            'exam_section_questions.accessor_type',
            'exam_section_questions.accessor_id',
            'exam_section_questions.option_1',
            'exam_section_questions.option_1_en',
            'exam_section_questions.option_2',
            'exam_section_questions.option_2_en',
            'exam_section_questions.option_3',
            'exam_section_questions.option_3_en',
            'exam_section_questions.option_4',
            'exam_section_questions.option_4_en',
            'exam_section_questions.answers as correct_answers',
            'exam_results.id as exam_result_id',
            'exam_results.answers',
            'exam_results.marks_achieved',
            'exam_results.file_paths',
        ]);

        $examSectionBuilder->where('exam_section_questions.exam_section_uuid', $examSection['uuid']);
        $examSectionBuilder->join("exam_results", function ($join) {
            $join->on('exam_results.exam_section_question_id', '=', 'exam_section_questions.uuid');
        });

        return $examSectionBuilder->get()->toArray();
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
     * @return array
     */
    public function storeExam(array $data): array
    {
        $examIds = [];
        if ($data['type'] == Exam::EXAM_TYPE_ONLINE) {
            $onlineExam = $this->storeOnlineExam($data);
            $examIds['online'] = $onlineExam->id;

        } else if ($data['type'] == Exam::EXAM_TYPE_OFFLINE) {
            $offlineExam = $this->storeOfflineExam($data);
            $examIds['offline'] = $offlineExam->id;

        } else {
            $data['online']['exam_type_id'] = $data['exam_type_id'];
            $data['offline']['exam_type_id'] = $data['exam_type_id'];

            $onlineExam = $this->storeOnlineExam($data['online']);
            $offlineExam = $this->storeOfflineExam($data['offline']);

            $examIds['online'] = $onlineExam->id;
            $examIds['offline'] = $offlineExam->id;
        }

        return $examIds;
    }

    /**
     * @param array $data
     * @return Exam
     */
    private function storeOnlineExam(array $data): Exam
    {
        $exam = app(Exam::class);
        $data['type'] = $data['type'] ?? Exam::EXAM_TYPE_ONLINE;
        $exam->fill($data);
        $exam->save();

        return $exam;
    }

    /**
     * @param $data
     * @return Exam
     */
    private function updateOnlineExam($data): Exam
    {
        $exam = Exam::findOrFail($data['exam_id']);
        $exam->fill($data);
        $exam->save();

        return $exam;
    }

    /**
     * @param $data
     * @return Exam
     */
    private function updateOfflineExam($data): Exam
    {
        $exam = Exam::findOrFail($data['exam_id']);
        $exam->fill($data);
        $exam->save();

        return $exam;
    }

    /**
     * @param array $data
     * @return Exam
     */
    private function storeOfflineExam(array $data): Exam
    {
        $exam = app(Exam::class);
        $data['type'] = $data['type'] ?? Exam::EXAM_TYPE_OFFLINE;
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
        if ($data['type'] == Exam::EXAM_TYPE_MIXED) {
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
                $examSetData['exam_id'] = $data['exam_ids']['offline'];
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
        if ($data['type'] == Exam::EXAM_TYPE_MIXED) {
            $this->storeOnlineExamSections($data['online']['exam_questions'], $data);
            $this->storeOfflineExamSections($data['offline']['exam_questions'], $data);
        } else if ($data['type'] == Exam::EXAM_TYPE_ONLINE) {
            $this->storeOnlineExamSections($data['exam_questions'], $data);
        } else {
            $this->storeOfflineExamSections($data['exam_questions'], $data);
        }
    }

    /**
     * @throws Throwable
     */
    private function storeOnlineExamSections($examQuestions, $data)
    {
        foreach ($examQuestions as $examSectionData) {
            $examSectionData['uuid'] = ExamSection::examSectionId();
            $examSectionData['exam_id'] = $data['exam_ids']['online'];
            if (!empty($data['row_status'])) {
                $examSectionData['row_status'] = $data['row_status'];
            }
            $examSection = app(ExamSection::class);
            $examSection->fill($examSectionData);
            $examSection->save();

            if ($examSectionData['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                $examSectionQuestionData = $examSectionData['questions'];
                $examSectionData['exam_type'] = Exam::EXAM_TYPE_ONLINE;
                $this->storeExamSectionQuestions($examSectionData, $examSectionQuestionData);
            }
        }

    }

    /**
     * @throws Throwable
     */
    private function storeOfflineExamSections(array $examQuestions, array $data)
    {
        foreach ($examQuestions as $examSectionData) {
            $examSectionData['uuid'] = ExamSection::examSectionId();
            $examSectionData['exam_id'] = $data['exam_ids']['offline'];
            if (!empty($data['row_status'])) {
                $examSectionData['row_status'] = $data['row_status'];
            }
            $examSection = app(ExamSection::class);
            $examSection->fill($examSectionData);
            $examSection->save();
            $examSectionData['exam_type'] = Exam::EXAM_TYPE_OFFLINE;
            $examSectionData['subject_id'] = $data['subject_id'];
            $examSectionData['sets'] = $data['sets'];

            if ($examSectionData['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                $this->storeExamSectionQuestions($examSectionData);
            } else {
                $examSectionQuestionData = $examSectionData['question_sets'];
                $this->storeExamSectionQuestions($examSectionData, $examSectionQuestionData);
            }

        }

    }

    private function storeOnlineExamSectionQuestions($examSectionData, $examSectionQuestionData)
    {
        foreach ($examSectionQuestionData as $examSectionQuestion) {
            $examSectionQuestion['uuid'] = ExamSectionQuestion::examSectionQuestionId();
            $examSectionQuestion['exam_id'] = $examSectionData['exam_id'];
            $examSectionQuestion['exam_section_uuid'] = $examSectionData['uuid'];
            $examSectionQuestion['question_selection_type'] = $examSectionData['question_selection_type'];
            $examSectionQuestion['question_id'] = $examSectionQuestion['id'];
            if (!empty($data['$examSectionData'])) {
                $examSectionQuestion['row_status'] = $examSectionData['row_status'];
            }
            $examQuestionSection = app(ExamSectionQuestion::class);
            $examQuestionSection->fill($examSectionQuestion);
            $examQuestionSection->save();
        }
    }

    /**
     * @throws Throwable
     */
    private function storeOfflineRandomExamSectionQuestions($examSectionData)
    {
        $questions = ExamQuestionBank::where('subject_id', $examSectionData['subject_id'])
            ->where('question_type', $examSectionData['question_type'])
            ->where('subject_id', $examSectionData['subject_id'])
            ->inRandomOrder()
            ->limit($examSectionData['number_of_questions'])
            ->get()
            ->toArray();
        throw_if(count($questions) != $examSectionData['number_of_questions'], ValidationException::withMessages([
            "Number Of " . ExamQuestionBank::EXAM_QUESTION_VALIDATION_MESSAGES[$examSectionData["question_type"]] . " questions in question bank for this subject must be at least " . $examSectionData['number_of_questions'] . "[42001]"
        ]));
        $individualMarks = $examSectionData['total_marks'] / floatval($examSectionData['number_of_questions']);
        foreach ($examSectionData['sets'] as $set => $value) {
            foreach ($questions as $question) {
                $question['uuid'] = ExamSectionQuestion::examSectionQuestionId();
                $question['exam_id'] = $examSectionData['exam_id'];
                $question['exam_section_uuid'] = $examSectionData['uuid'];
                $question['exam_set_uuid'] = $value;
                $question['question_selection_type'] = $examSectionData['question_selection_type'];
                $question['individual_marks'] = $individualMarks;
                $question['question_id'] = $question['id'];
                if (!empty($examSectionData['row_status'])) {
                    $question['row_status'] = $examSectionData['row_status'];
                }
                $examQuestionSection = app(ExamSectionQuestion::class);
                $examQuestionSection->fill($question);
                $examQuestionSection->save();
            }
        }


    }

    private function storeOfflineExamSectionQuestions($examSectionData, $examSectionQuestionData)
    {
        foreach ($examSectionQuestionData as $examSectionQuestionSet) {
            foreach ($examSectionQuestionSet['questions'] as $examSectionQuestion) {
                $examSectionQuestion['uuid'] = ExamSectionQuestion::examSectionQuestionId();
                $examSectionQuestion['exam_id'] = $examSectionData['exam_id'];
                $examSectionQuestion['exam_section_uuid'] = $examSectionData['uuid'];
                $examSectionQuestion['exam_set_uuid'] = $examSectionData['sets'][$examSectionQuestionSet['id']];
                $examSectionQuestion['question_selection_type'] = $examSectionData['question_selection_type'];
                $examSectionQuestion['question_id'] = $examSectionQuestion['id'];
                if (!empty($examSectionData['row_status'])) {
                    $examSectionQuestion['row_status'] = $examSectionData['row_status'];
                }
                $examQuestionSection = app(ExamSectionQuestion::class);
                $examQuestionSection->fill($examSectionQuestion);
                $examQuestionSection->save();
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
            $this->storeOnlineExamSectionQuestions($examSectionData, $examSectionQuestionData);
        }
        if ($examSectionData['exam_type'] == Exam::EXAM_TYPE_OFFLINE) {
            if ($examSectionData['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                $this->storeOfflineRandomExamSectionQuestions($examSectionData);

            } else {
                $this->storeOfflineExamSectionQuestions($examSectionData, $examSectionQuestionData);
            }
        }
    }

    /**
     * @param ExamType $examType
     * @param array $data
     * @return ExamType
     */
    public function updateExamType(ExamType $examType, array $data): ExamType
    {
        $examType->fill($data);
        $examType->save();
        return $examType;
    }

    /**
     * @param array $data
     * @return array
     */
    public function updateExam(array $data): array
    {
        if ($data['type'] == Exam::EXAM_TYPE_ONLINE) {
            $onlineExam = $this->updateOnlineExam($data);
            $examIds['online'] = $onlineExam->id;

        } else if ($data['type'] == Exam::EXAM_TYPE_OFFLINE) {
            $offlineExam = $this->updateOfflineExam($data);
            $examIds['offline'] = $offlineExam->id;

        } else {
            $onlineExam = $this->updateOnlineExam($data['online']);
            $offlineExam = $this->updateOfflineExam($data['offline']);

            $examIds['online'] = $onlineExam->id;
            $examIds['offline'] = $offlineExam->id;
        }
        return $examIds;
    }

    public function deleteExamRelatedDataForUpdate(array $examIds)
    {
        if (!empty($examIds['online'])) {
            ExamSection::where('exam_id', $examIds['online'])->delete();
            ExamSectionQuestion::where('exam_id', $examIds['online'])->delete();
        }
        if (!empty($examIds['offline'])) {
            ExamSection::where('exam_id', $examIds['offline'])->delete();
            ExamSet::where('exam_id', $examIds['offline'])->delete();
            ExamSectionQuestion::where('exam_id', $examIds['offline'])->delete();
        }
    }

    /**
     * @param array $data
     * @return void
     */

    public function youthExamMarkUpdate(array $data): void
    {
        $youthId = $data['youth_id'];
        $examId = $data['exam_id'];

        foreach ($data['marks'] as $mark) {
            $examResultId = $mark['exam_result_id'];
            $examResult = ExamResult::findOrFail($examResultId);
            $examResult->marks_achieved = $mark['marks_achieved'];
            $examResult->youth_id = $youthId;
            $examResult->exam_id = $examId;
            $examResult->save();

        }

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

    /**
     * @param array $request
     * @param int $id
     * @return array
     */
    public function getExamYouthList(array $request, int $id): array
    {
        $youthId = $request['youth_id'] ?? "";
        $pageSize = $request['page_size'] ?? BaseModel::DEFAULT_PAGE_SIZE;
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $response = [];

        /** @var ExamResult|Builder $examResultBuilder */
        $examResultBuilder = ExamResult::select([
            "exam_results.id",
            "exam_results.exam_id",
            "exam_results.youth_id",
            "exam_results.exam_section_question_id",
            "exam_results.answers",
            "exam_results.file_paths",
        ]);

        $examResultBuilder->groupBy("exam_results.youth_id");
        $examResultBuilder->selectRaw('sum(exam_results.marks_achieved) as marks_achieved');
        $examResultBuilder->where('exam_results.exam_id', $id);

        if (!empty($youthId)) {
            $examResultBuilder->where('exam_results.youth_id', 'like', '%' . $youthId . '%');
        }

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $candidates = $examResultBuilder->paginate($pageSize);
            $paginateData = (object)$candidates->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $candidates = $examResultBuilder->get();
        }

        $resultArray = $candidates->toArray();

        $youthIds = ExamResult::where('exam_id', $id)->pluck('youth_id')->unique()->toArray();

        $youthProfiles = !empty($youthIds) ? ServiceToServiceCall::getYouthProfilesByIds($youthIds) : [];

        $indexedYouths = [];

        foreach ($youthProfiles as $item) {
            $id = $item['id'];
            $indexedYouths[$id] = $item;
        }

        foreach ($resultArray["data"] as &$item) {
            $id = $item['youth_id'];
            $youthData = $indexedYouths[$id];
            $item['youth_profile'] = $youthData;
        }


        $resultData = $resultArray['data'] ?? $resultArray;

        $response['order'] = $order;
        $response['data'] = $resultData;

        return $response;
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        /** If multiple purpose is added ,then add purpose table them conditionally */
        $examPurposeTable = ExamType::EXAM_PURPOSE_TABLE_BATCH;

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
            /** exam type online part validation rules**/
            $rules['online'] = [
                'array',
                'required'
            ];
            $examValidationRules = $this->examValidationRules('online.', $id);
            $rules = array_merge($rules, $examValidationRules);
            $examSectionValidationRules = $this->examSectionValidationRules('online.');
            $rules = array_merge($rules, $examSectionValidationRules);
            if (!empty($data['online']['exam_questions'])) {
                $onlineExamQuestionRules = $this->onlineExamQuestionValidationRules($data['online']['exam_questions'], 'online.');
                $rules = array_merge($rules, $onlineExamQuestionRules);
            }


            /** exam type offline part validation rules**/
            $rules['offline'] = [
                'array',
                'required'
            ];
            $examValidationRules = $this->examValidationRules('offline.', $id);
            $rules = array_merge($rules, $examValidationRules);
            $examSectionValidationRules = $this->examSectionValidationRules('offline.');
            $rules = array_merge($rules, $examSectionValidationRules);
            $examSetValidationRules = $this->examSetValidationRules($data, "offline.");
            $rules = array_merge($rules, $examSetValidationRules);

            if (!empty($data['offline']['exam_questions'])) {
                if (!empty($data['offline']['sets'])) {
                    $numberOfSets = count($data['offline']['sets']);
                }
                $offlineExamQuestionRules = $this->offlineExamQuestionValidationRules($data['offline']['exam_questions'], $numberOfSets, "offline.");
                $rules = array_merge($rules, $offlineExamQuestionRules);
            }


        } else {
            $examValidationRules = $this->examValidationRules('', $id);
            $rules = array_merge($rules, $examValidationRules);
            $examSectionValidationRules = $this->examSectionValidationRules();
            $rules = array_merge($rules, $examSectionValidationRules);

            if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_ONLINE) {
                if (!empty($data['exam_questions'])) {
                    $onlineExamQuestionRules = $this->onlineExamQuestionValidationRules($data['exam_questions']);
                    $rules = array_merge($rules, $onlineExamQuestionRules);
                }
            }

            if (!empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE) {
                if (!empty($data['sets'])) {
                    $numberOfSets = count($data['sets']);
                }
                if (!empty($data['exam_questions'])) {
                    if (!empty($data['sets'])) {
                        $numberOfSets = count($data['sets']);
                    }
                    $offlineExamQuestionRules = $this->offlineExamQuestionValidationRules($data['exam_questions'], $numberOfSets);
                    $rules = array_merge($rules, $offlineExamQuestionRules);

                }
                $examSetValidationRules = $this->examSetValidationRules($data);
                $rules = array_merge($rules, $examSetValidationRules);
            }
        }


        return Validator::make($data, $rules, $customMessage);
    }

    /**
     * @param array $examQuestions
     * @param int $numberOfSets
     * @param string $examType
     * @return array
     */
    public function offlineExamQuestionValidationRules(array $examQuestions, int $numberOfSets = 0, string $examType = ''): array
    {
        $rules = [];
        $outerIndex = 0;
        foreach ($examQuestions as $examQuestion) {
            if (!empty($examQuestion) && !empty($examQuestion['question_selection_type']) && $examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_FIXED) {
                $offlineExamQuestionNumbers = $examQuestion['number_of_questions'];
            } else if (!empty($examQuestion) && !empty($examQuestion['question_selection_type']) && $examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_SELECTED_QUESTIONS) {
                $offlineExamQuestionNumbers = $examQuestion['number_of_questions'] + 1;
            } else {
                $offlineExamQuestionNumbers = 0;
            }
            if (!empty($examQuestion['question_selection_type']) && $examQuestion['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets'] = [
                    'required',
                    'array',
                    'size:' . $numberOfSets
                ];
                $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.*'] = [
                    'required',
                    'array',
                ];
                if (!empty($examQuestion['question_sets'])) {
                    $index = 0;
                    foreach ($examQuestion['question_sets'] as $examQuestionSet) {
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.id'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'string',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'array',
                            'size:' . $offlineExamQuestionNumbers
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'array',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.id'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'integer',
                            'distinct',
                            'exists:exam_question_banks,id,deleted_at,NULL',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.title_en'] = [
                            'nullable',
                            'string',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.title'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'string',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.individual_marks'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'numeric',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.accessor_type'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'string',
                            'max:100',
                            Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.accessor_id'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'int',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.subject_id'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'int',
                            'exists:exam_subjects,id,deleted_at,NULL'
                        ];

                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.question_type'] = [
                            Rule::requiredIf(!empty($examQuestionSet)),
                            'nullable',
                            'int',
                            Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
                        ];

                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_1'] = [
                            Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                            'nullable',
                            'string',
                            'max:600'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_1_en'] = [
                            'nullable',
                            'string',
                            'max:300'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_2'] = [
                            Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                            'nullable',
                            'string',
                            'max:600'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_2_en'] = [
                            'nullable',
                            'string',
                            'max:300'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_3'] = [
                            Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                            'nullable',
                            'string',
                            'max:600'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_3_en'] = [
                            'nullable',
                            'string',
                            'max:300'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_4'] = [
                            Rule::requiredIf(!empty($examQuestionSet) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                            'nullable',
                            'string',
                            'max:600'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.option_4_en'] = [
                            'nullable',
                            'string',
                            'max:300'
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.answers'] = [
                            Rule::requiredIf(!empty($examQuestionSet) && in_array($examQuestion['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                            'nullable',
                            'array',
                        ];
                        $rules[$examType . 'exam_questions.' . $outerIndex . '.question_sets.' . $index . '.questions.*.answers.*'] = [
                            Rule::requiredIf(!empty($examQuestionSet) && in_array($examQuestion['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                            'nullable',
                            'string',
                        ];
                        $index++;
                    }

                }

            }
            $outerIndex++;
        }

        return $rules;
    }

    /**
     * @param string $examType
     * @param int|null $id
     * @return array
     */
    public function examValidationRules(string $examType = '', int $id = null): array
    {
        $rules = [];
        $rules[$examType . 'exam_id'] = [
            Rule::requiredIf($id != null),
            'nullable',
            'int',
            'exists:exams,id,deleted_at,NULL'
        ];
        $rules[$examType . 'exam_date'] = [
            'required',
            'date_format:Y-m-d H:i:s'
        ];
        $rules[$examType . 'duration'] = [
            'required',
            'int'
        ];
        $rules[$examType . 'venue'] = [
            'nullable',
            'string',
        ];
        $rules[$examType . 'total_marks'] = [
            'required',
            'numeric',
        ];
        return $rules;
    }

    /**
     * @param string $examType
     * @return array
     */
    public function examSectionValidationRules(string $examType = ''): array
    {
        $rules = [];
        $rules[$examType . 'exam_questions'] = [
            'array',
            'required',
        ];
        $rules[$examType . 'exam_questions.*'] = [
            'array',
            'required',
        ];
        $rules[$examType . 'exam_questions.*.question_type'] = [
            'integer',
            'required',
            'distinct',
            Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
        ];
        $rules[$examType . 'exam_questions.*.number_of_questions'] = [
            'integer',
            'required',
        ];
        $rules[$examType . 'exam_questions.*.total_marks'] = [
            'numeric',
            'required',
        ];
        $rules[$examType . 'exam_questions.*.question_selection_type'] = [
            'integer',
            'required',
            Rule::in(ExamQuestionBank::QUESTION_SELECTION_TYPES)
        ];

        return $rules;

    }

    /**
     * @param array $data
     * @param string $examType
     * @return array
     */
    public
    function examSetValidationRules(array $data, string $examType = ""): array
    {
        $rules = [];
        $rules[$examType . "sets"] = [
            Rule::requiredIf(function () use ($data) {
                return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
            }),
            "nullable",
            "array",
        ];
        $rules[$examType . "sets.*.id"] = [
            Rule::requiredIf(function () use ($data) {
                return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
            }),
            'nullable',
            'string',
            "distinct",
        ];
        $rules[$examType . "sets.*.title"] = [
            Rule::requiredIf(function () use ($data) {
                return !empty($data['type']) && $data['type'] == Exam::EXAM_TYPE_OFFLINE;
            }),
            'string',
            'nullable',
        ];
        $rules[$examType . "sets.*.title_en"] = [
            "nullable",
            'string',
        ];

        return $rules;
    }


    /**
     * @param array $examQuestions
     * @param string $examType
     * @return array
     */
    public function onlineExamQuestionValidationRules(array $examQuestions, string $examType = ''): array
    {
        $index = 0;
        $rules = [];
        foreach ($examQuestions as $examQuestion) {
            if (!empty($examQuestion) && !empty($examQuestion['question_selection_type']) && $examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_FIXED) {
                $onlineExamQuestionNumbers = $examQuestion['number_of_questions'];
            } else if (!empty($examQuestion) && !empty($examQuestion['question_selection_type']) && $examQuestion['question_selection_type'] == ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_SELECTED_QUESTIONS) {
                $onlineExamQuestionNumbers = $examQuestion['number_of_questions'] + 1;
            } else {
                $onlineExamQuestionNumbers = 0;
            }
            if (!empty($examQuestion['question_selection_type']) && $examQuestion['question_selection_type'] != ExamQuestionBank::QUESTION_SELECTION_RANDOM_FROM_QUESTION_BANK) {
                $rules[$examType . 'exam_questions.' . $index . '.questions'] = [
                    'required',
                    'array',
                    'size:' . $onlineExamQuestionNumbers
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*'] = [
                    'required',
                    'array',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.id'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'integer',
                    'distinct',
                    'exists:exam_question_banks,id,deleted_at,NULL',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.title'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'string',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.title'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'string',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.title_en'] = [
                    'nullable',
                    'string',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.individual_marks'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'nullable',
                    'numeric',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.accessor_type'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'string',
                    'max:100',
                    Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.accessor_id'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'int',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.subject_id'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'int',
                    'exists:exam_subjects,id,deleted_at,NULL'
                ];

                $rules[$examType . 'exam_questions.' . $index . '.questions.*.question_type'] = [
                    Rule::requiredIf(!empty($examQuestion['questions'])),
                    'int',
                    Rule::in(ExamQuestionBank::EXAM_QUESTION_TYPES)
                ];

                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_1'] = [
                    Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_1_en'] = [
                    'nullable',
                    'string',
                    'max:300'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_2'] = [
                    Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_2_en'] = [
                    'nullable',
                    'string',
                    'max:300'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_3'] = [
                    Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_3_en'] = [
                    'nullable',
                    'string',
                    'max:300'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_4'] = [
                    Rule::requiredIf(!empty($examQuestion['questions']) && $examQuestion['question_type'] == ExamQuestionBank::EXAM_QUESTION_TYPE_MCQ),
                    'nullable',
                    'string',
                    'max:600'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.option_4_en'] = [
                    'nullable',
                    'string',
                    'max:300'
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.answers'] = [
                    Rule::requiredIf(!empty($examQuestion['questions']) && in_array($examQuestion['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                    'nullable',
                    'array',
                ];
                $rules[$examType . 'exam_questions.' . $index . '.questions.*.answers.*'] = [
                    Rule::requiredIf(!empty($examQuestion['questions']) && in_array($examQuestion['question_type'], ExamQuestionBank::ANSWER_REQUIRED_QUESTION_TYPES)),
                    'nullable',
                    'string',
                ];

            }
            $index++;

        }
        return $rules;

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

    public function getExamFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'purpose_name' => [
                'required',
                'string',
                Rule::in(ExamType::EXAM_PURPOSES)
            ],
        ];

        return Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    function examYouthListFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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
            'youth_id' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ]

        ];

        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param $examId
     * @param $youthId
     * @return array
     */
    public function getPreviewYouthExam($examId, $youthId): array
    {

        $examPreviewBuilder = Exam::select([
            'exam_types.subject_id',
            'exam_subjects.title as subject_title',
            'exam_subjects.title_en as subject_title_en',
            'exam_types.title',
            'exam_types.title_en',
            'exams.duration',
            'exams.exam_date',
            'exams.total_marks'

        ]);
        $examPreviewBuilder->where('exams.id', $examId);

        $examPreviewBuilder->join("exam_types", function ($join) {
            $join->on('exam_types.id', '=', 'exams.exam_type_id')
                ->whereNull('exam_types.deleted_at');
        });

        $examPreviewBuilder->join("exam_subjects", function ($join) {
            $join->on('exam_types.subject_id', '=', 'exam_subjects.id')
                ->whereNull('exam_subjects.deleted_at');
        });

        $examPreview = $examPreviewBuilder->firstOrFail()->toArray();


        $youthIds = [];
        array_push($youthIds, $youthId);

        $youthProfiles = !empty($youthIds) ? ServiceToServiceCall::getYouthProfilesByIds($youthIds) : [];

        $examPreview['first_name'] = $youthProfiles[0]['first_name'];
        $examPreview['first_name_en'] = $youthProfiles[0]['first_name_en'];
        $examPreview['last_name'] = $youthProfiles[0]['last_name'];
        $examPreview['last_name_en'] = $youthProfiles[0]['last_name_en'];
        $examPreview['mobile'] = $youthProfiles[0]['mobile'];
        $examPreview['email'] = $youthProfiles[0]['email'];

        $examPreview['exam_sections'] = $this->getExamSectionByExam($examId);


        foreach ($examPreview['exam_sections'] as &$examSection) {
            $examSection['subject_id'] = $examPreview['subject_id'];
            $examSection['questions'] = $this->getExamSectionQuestionWithAnswerBySection($examSection);
        }
        return $examPreview;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function examPaperSubmitValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        if (!empty($data["answers"])) {
            $data["answers"] = isset($data['answers']) && is_array($data['answers']) ? $data['answers'] : explode(',', $data['answers']);
        }
        $rules = [
            'exam_id' => [
                'required',
                'int',
                'exists:exams,id,deleted_at,NULL'
            ],
            'youth_id' => [
                'int',
                'required'
            ],
            'questions' => [
                'required',
                'array',
            ],
            'questions.*' => [
                'required',
                'array',
            ],
            'questions.*.exam_section_question_id' => [
                'nullable',
                'string',
                'exists:exam_section_questions,uuid,deleted_at,NULL'
            ],
            'questions.*.question_id' => [
                'required',
                'int',
                'exists:exam_question_banks,id,deleted_at,NULL'
            ],
            'questions.*.individual_marks' => [
                'required',
                'numeric',
            ],
            'questions.*.answers' => [
                'nullable',
                'array',
            ],
            'questions.*.answers.*' => [
                'nullable',
                'string',
            ],
            'questions.*.file_path' => [
                'nullable',
                'string',
            ],
        ];
        return Validator::make($data, $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function youthExamMarkUpdateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Order must be either ASC or DESC. [30000]',
        ];
        $rules = [

            'exam_id' => [
                'required',
                'int',
                'min:1',
                'exists:exams,id,deleted_at,NULL'
            ],
            'youth_id' => [
                'required',
                'int',
                'min:1'
            ],
            'marks' => [
                'array',
                'required',
            ],
            'marks.*' => [
                'array',
                'required',
            ],
            'marks.*.exam_result_id' => [
                'integer',
                'required',
                'exists:exam_results,id'
            ],
            'marks.*.marks_achieved' => [
                'numeric',
                'required',
            ]

        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }


}

