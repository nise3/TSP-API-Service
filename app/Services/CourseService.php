<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Skill;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

/**
 * Class CourseService
 * @package App\Services
 */
class CourseService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public const COURSE_FILTER_POPULAR = "popular";
    public const COURSE_FILTER_RECENT = "recent";
    public const COURSE_FILTER_NEARBY = "nearby";
    public const COURSE_FILTER_SKILL_MATCHING = "skill-matching";
    public const COURSE_FILTER_TRENDING = "trending";
    public const COURSE_FILTER_AVAILABILITY_RUNNING = 1;
    public const COURSE_FILTER_AVAILABILITY_UPCOMING = 2;
    public const COURSE_FILTER_AVAILABILITY_COMPLETED = 3;
    public const COURSE_FILTER_COURSE_TYPE_PAID = 1;
    public const COURSE_FILTER_COURSE_TYPE_FREE = 2;

    public function getCourseList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.level',
                'courses.language_medium',
                'courses.institute_id',
                'institutes.title as institute_title',
                'institutes.title_en as institute_title_en',
                'courses.branch_id',
                'branches.title as branch_title',
                'branches.title_en as branch_title_en',
                'courses.program_id',
                'programs.title as program_title',
                'programs.title_en as program_title_en',
                'courses.title',
                'courses.title_en',
                'courses.course_fee',
                'courses.duration',
                'courses.overview',
                'courses.overview_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.objectives',
                'courses.objectives_en',
                'courses.lessons',
                'courses.lessons_en',
                'courses.training_methodology',
                'courses.training_methodology_en',
                'courses.evaluation_system',
                'courses.evaluation_system_en',
                'courses.prerequisite',
                'courses.prerequisite_en',
                'courses.eligibility',
                'courses.eligibility_en',
                'courses.cover_image',
                'courses.application_form_settings',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
            ]
        );

        $coursesBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }*/
        });

        $coursesBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('courses.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }*/
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('programs.row_status', $rowStatus);
            }*/
        });

        $coursesBuilder->orderBy('courses.id', $order);

        if (is_numeric($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $titleBn . '%');
        }

        if (is_numeric($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        /** @var Collection $courses */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $courses = $coursesBuilder->paginate($pageSize);
            $paginateData = (object)$courses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courses = $coursesBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @param bool $withTrainers
     * @return array
     */
    public function getOneCourse(int $id, Carbon $startTime, bool $withTrainers = false): array
    {
        /** @var Course|Builder $courseBuilder */
        $courseBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.level',
                'courses.language_medium',
                'courses.institute_id',
                'institutes.title as institute_title',
                'institutes.title_en as institute_title_en',
                'courses.branch_id',
                'branches.title as branch_title',
                'branches.title_en as branch_title_en',
                'courses.program_id',
                'programs.title as program_title',
                'programs.title_en as program_title_en',
                'courses.title',
                'courses.title_en',
                'courses.course_fee',
                'courses.duration',
                'courses.overview',
                'courses.overview_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.objectives',
                'courses.objectives_en',
                'courses.lessons',
                'courses.lessons_en',
                'courses.training_methodology',
                'courses.training_methodology_en',
                'courses.evaluation_system',
                'courses.evaluation_system_en',
                'courses.prerequisite',
                'courses.prerequisite_en',
                'courses.eligibility',
                'courses.eligibility_en',
                'courses.cover_image',
                'courses.application_form_settings',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
            ]
        );

        $courseBuilder->join("institutes", function ($join) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $courseBuilder->leftJoin("branches", function ($join) {
            $join->on('courses.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $courseBuilder->leftJoin("programs", function ($join) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
        });

        $courseBuilder->where('courses.id', '=', $id);

        $courseBuilder->with('skills');

        /** @var Course $course */
        $course = $courseBuilder->firstOrFail();

        if ($withTrainers == true) {
            /** @var Builder $trainerBuilder */
            $trainerBuilder = Trainer::select([
                'trainers.trainer_name',
                'trainers.trainer_name_en',
                'trainers.email',
                'trainers.mobile',
            ]);
            $trainerBuilder->join('trainer_batch', 'trainer_batch.trainer_id', '=', 'trainers.id');
            $trainerBuilder->join('batches', 'trainer_batch.batch_id', '=', 'batches.id');
            $trainerBuilder->where('batches.course_id', $id);
            $trainerBuilder->orderBy('trainers.id', 'ASC');
            $trainerBuilder->groupBy('trainers.id');

            /** @var Collection $trainers */
            $trainers = $trainerBuilder->get();

            $course["trainers"] = $trainers->toArray();

        }

        return [
            "data" => $course ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param array $data
     * @return Course
     */
    public function store(array $data): Course
    {
        $course = new Course();
        $course->fill($data);
        $course->save();

        if(!empty($data["skills"])){
            $this->assignSkills($course, $data["skills"]);
        }

        return $course;
    }

    /**
     * @param Course $course
     * @param array $data
     * @return Course
     */
    public function update(Course $course, array $data): Course
    {
        $course->fill($data);
        $course->save();
        $this->assignSkills($course, $data["skills"]);
        return $course;
    }

    /**
     * @param Course $course
     * @return bool
     */
    public function destroy(Course $course): bool
    {
        return $course->delete();
    }


    public function getCourseTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title');
        $paginate = $request->query('page');
        $order = $request->filled('order') ? $request->query('order') : 'ASC';

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::onlyTrashed()->select(
            [
                'courses.id as id',
                'courses.code',
                'courses.institute_id',
                'institutes.title_en as institute_title_en',
                'institutes.title as institute_title',
                'courses.title_en',
                'courses.title',
                'courses.course_fee',
                'courses.duration',
                'courses.overview',
                'courses.target_group',
                'courses.objectives',
                'courses.lessons',
                'courses.training_methodology',
                'courses.evaluation_system',
                'courses.prerequisite',
                'courses.eligibility',
                'courses.cover_image',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
            ]
        );
        $coursesBuilder->join('institutes', 'courses.institute_id', '=', 'institutes.id');

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $courses */
        if (is_numeric($paginate) || is_numeric($limit)) {
            $limit = $limit ?: 10;
            $courses = $coursesBuilder->paginate($limit);
            $paginateData = (object)$courses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courses = $coursesBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    public function restore(Course $course): bool
    {
        return $course->restore();
    }

    public function forceDelete(Course $courses): bool
    {
        return $courses->forceDelete();
    }

    /** Filter courses by popular, recent, nearby, skill matching */
    public function getFilterCourses(array $request, Carbon $startTime, string $type = null): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $curDate = Carbon::now();

        /** Filters variables */
        $locDistrictId = $request['loc_district_id'] ?? "";
        $locUpazilaId = $request['loc_upazila_id'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $instituteId = $request['institute_id'] ?? "";
        $programId = $request['program_id'] ?? "";
        $availability = $request['availability'] ?? "";
        $language = $request['language_medium'] ?? "";
        $courseType = $request['course_type'] ?? "";
        $courseName = $request['course_name'] ?? "";
        $courseLevel = $request['level'] ?? "";

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.title_en',
                'courses.title',
                'courses.institute_id',
                'institutes.title as institute_title',
                'institutes.title_en as institute_title_en',
                'courses.program_id',
                'programs.title as program_title',
                'programs.title_en as program_title_en',
                'courses.course_fee',
                'courses.duration',
                'courses.overview',
                'courses.overview_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.prerequisite',
                'courses.prerequisite_en',
                'courses.eligibility',
                'courses.eligibility_en',
                'courses.cover_image',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
                DB::raw('COUNT(distinct course_enrollments.id) as total_enroll')
            ]
        );

        $coursesBuilder->join("institutes", function ($join) use ($rowStatus, $instituteId) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            /*if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }*/
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus, $programId) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
            /*if ($rowStatus) {
                $join->where('programs.row_status', $rowStatus);
            }*/
        });

        if (is_numeric($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (is_numeric($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        if (is_numeric($programId)) {
            $coursesBuilder->where('courses.program_id', '=', $programId);
        }

        if (is_numeric($language)) {
            $coursesBuilder->where('courses.language_medium', '=', $language);
        }

        if (is_numeric($courseType)) {
            if ($courseType == self::COURSE_FILTER_COURSE_TYPE_PAID) {
                $coursesBuilder->where('courses.course_fee', '!=', 0);
                $coursesBuilder->Where('courses.course_fee', '!=', null);
            } else if ($courseType == self::COURSE_FILTER_COURSE_TYPE_FREE) {
                $coursesBuilder->where('courses.course_fee', '=', 0);
                $coursesBuilder->orWhere('courses.course_fee', '=', null);
            }
        }

        if (!empty($courseName)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $courseName . '%');
            $coursesBuilder->orWhere('courses.title_en', 'like', '%' . $courseName . '%');
        }

        if (is_numeric($courseLevel)) {
            $coursesBuilder->where('courses.level', '=', $courseLevel);
        }

        $coursesBuilder->leftJoin("course_enrollments", "courses.id", "=", "course_enrollments.course_id");

        $coursesBuilder->join("batches", "courses.id", "=", "batches.course_id");

        if ($type == self::COURSE_FILTER_POPULAR || $type == self::COURSE_FILTER_RECENT || is_numeric($availability)) {
            if ($type == self::COURSE_FILTER_POPULAR || $availability == self::COURSE_FILTER_AVAILABILITY_RUNNING) {
                $coursesBuilder->whereDate('batches.registration_start_date', '<=', $curDate);
                $coursesBuilder->whereDate('batches.registration_end_date', '>=', $curDate);
            }
            if ($type != self::COURSE_FILTER_POPULAR && ($type == self::COURSE_FILTER_RECENT || $availability == self::COURSE_FILTER_AVAILABILITY_UPCOMING)) {
                $coursesBuilder->WhereDate('batches.registration_start_date', '>', $curDate);
            }
            if ($availability == self::COURSE_FILTER_AVAILABILITY_COMPLETED) {
                $coursesBuilder->whereDate('batches.batch_end_date', '<', $curDate);
            }
        }

        if ($type == self::COURSE_FILTER_NEARBY) {
            $coursesBuilder->join('training_centers', 'training_centers.id', '=', 'batches.training_center_id');
            if ($locUpazilaId) {
                $coursesBuilder->where('training_centers.loc_upazila_id', '=', $locUpazilaId);
            } else if ($locDistrictId) {
                $coursesBuilder->where('training_centers.loc_district_id', '=', $locDistrictId);
            } else {
                $response['data'] = [];
                $response['_response_status'] = [
                    "success" => true,
                    "code" => Response::HTTP_OK,
                    "query_time" => $startTime->diffInSeconds(Carbon::now()),
                ];

                return $response;
            }
        }

        if ($type == self::COURSE_FILTER_SKILL_MATCHING) {
            if (is_array($skillIds) && count($skillIds) > 0) {
                $coursesBuilder->join('course_skill', 'course_skill.course_id', '=', 'courses.id');
                $coursesBuilder->whereIn('course_skill.skill_id', $skillIds);
            } else {
                $response['data'] = [];
                $response['_response_status'] = [
                    "success" => true,
                    "code" => Response::HTTP_OK,
                    "query_time" => $startTime->diffInSeconds(Carbon::now()),
                ];

                return $response;
            }
        }

        if ($type == self::COURSE_FILTER_TRENDING) {
            $randomIds = [];
            for ($i = 0; $i < 10; $i++) {
                $id = rand(1, 20);
                if (in_array($id, $randomIds)) {
                    $i--;
                    continue;
                }
                array_push($randomIds, $id);
            }
            $coursesBuilder->whereIn('courses.id', $randomIds);
        }

        $coursesBuilder->groupBy("courses.id");
        $coursesBuilder->orderByDesc('total_enroll');


        /** @var Collection $courses */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $courses = $coursesBuilder->paginate($pageSize);
            $paginateData = (object)$courses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;

        } else if ($type == self::COURSE_FILTER_POPULAR || $type == self::COURSE_FILTER_RECENT) {
            $courses = $coursesBuilder->get()->take(20);
        } else {
            $courses = $coursesBuilder->get();
        }

        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    private function assignSkills(Course $course, array $skills)
    {
        /** Assign skills to COURSE */
        $skillIds = Skill::whereIn("id", $skills)
            ->orderBy('id', 'ASC')
            ->pluck('id')
            ->toArray();
        $course->skills()->sync($skillIds);

    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $requestData = $request->all();

        if (!empty($requestData["skills"])) {
            $requestData["skills"] = is_array($requestData['skills']) ? $requestData['skills'] : explode(',', $requestData['skills']);
        }

        $customMessage = [
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        $rules = [
            'code' => [
                'required',
                'string',
                'max:150',
                'unique:courses,code,' . $id,
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id,deleted_at,NULL',
            ],
            'branch_id' => [
                'nullable',
                'int',
                'exists:branches,id,deleted_at,NULL',
            ],
            'program_id' => [
                'nullable',
                'int',
                'exists:programs,id,deleted_at,NULL',
            ],
            'title' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            "level" => [
                'required',
                'int',
                Rule::in(Course::COURSE_LEVELS)
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:255',
                'min:2'
            ],
            'course_fee' => [
                'nullable',
                'numeric',
            ],
            'duration' => [
                'nullable',
                'numeric',
            ],
            'overview' => [
                'nullable',
                'string'
            ],
            'overview_en' => [
                'nullable',
                'string'
            ],
            'target_group' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'target_group_en' => [
                'nullable',
                'string',
                'max: 500',
            ],
            'objectives' => [
                'nullable',
                'string'
            ],
            'objectives_en' => [
                'nullable',
                'string'
            ],
            'lessons' => [
                'nullable',
                'string'
            ],
            'lessons_en' => [
                'nullable',
                'string'
            ],
            "language_medium" => [
                "required",
                Rule::in(Course::COURSE_LANGUAGE_MEDIUMS)
            ],
            'training_methodology' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'training_methodology_en' => [
                'nullable',
                'string',
                'max:600',
            ],
            'evaluation_system' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'evaluation_system_en' => [
                'nullable',
                'string',
                'max:500',
            ],
            'prerequisite' => [
                'nullable',
                'string'
            ],
            'prerequisite_en' => [
                'nullable',
                'string'
            ],
            'eligibility' => [
                'nullable',
                'string',
            ],
            'eligibility_en' => [
                'nullable',
                'string',
            ],
            'cover_image' => [
                'nullable',
                'string'
            ],
            'application_form_settings' => [
                'nullable',
                'string',
            ],
            "skills" => [
                "required",
                "array",
                "min:1",
                "max:10"
            ],
            "skills.*" => [
                "required",
                'integer',
                "distinct",
                "min:1"
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];
        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param $type
     * @return Validator
     */
    public function filterValidator(Request $request, $type = null): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        $requestData = $request->all();

        if (!empty($requestData['skill_ids'])) {
            $requestData['skill_ids'] = is_array($requestData['skill_ids']) ? $requestData['skill_ids'] : explode(',', $requestData['skill_ids']);
        }

        $rules = [
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'institute_id' => 'nullable|int|gt:0',
            'program_id' => 'nullable|int|gt:0',
            'course_name' => 'nullable|string'
        ];

        if (isset($requestData['availability'])) {
            $rules['availability'] = [
                'nullable',
                'integer',
                Rule::in(Course::COURSE_FILTER_AVAILABILITIES)
            ];
        }

        if (isset($requestData['language_medium'])) {
            $rules['language_medium'] = [
                'nullable',
                'integer',
                Rule::in(Course::COURSE_LANGUAGE_MEDIUMS)
            ];
        }

        if (isset($requestData['course_type'])) {
            $rules['course_type'] = [
                'nullable',
                'integer',
                Rule::in(Course::COURSE_FILTER_COURSE_TYPES)
            ];
        }

        if (isset($requestData['level'])) {
            $rules['level'] = [
                'nullable',
                'integer',
                Rule::in(Course::COURSE_LEVELS)
            ];
        }

        if ($type && $type == Course::COURSE_FILTER_TYPE_NEARBY) {
            $rules['loc_district_id'] = [
                Rule::requiredIf(function () use ($requestData) {
                    return (!isset($requestData['loc_upazila_id']));
                }),
                'nullable',
                'integer'
            ];
            $rules['loc_upazila_id'] = [
                'nullable',
                'integer'
            ];
        }

        if ($type && $type == Course::COURSE_FILTER_TYPE_SKILL_MATCHING) {
            $rules['skill_ids'] = [
                'required',
                'array',
                'min:1',
                'max:10'
            ];
            $rules['skill_ids.*'] = [
                'required',
                'integer',
                'distinct',
                'min:1'
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }
}
