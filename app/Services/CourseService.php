<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Youth;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
                'courses.description',
                'courses.description_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.objectives',
                'courses.objectives_en',
                'courses.contents',
                'courses.contents_en',
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
            if (is_int($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $coursesBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('courses.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('programs.row_status', $rowStatus);
            }
        });

        $coursesBuilder->orderBy('courses.id', $order);

        if (is_int($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $titleBn . '%');
        }

        if (is_int($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        /** @var Collection $courses */
        if (is_int($paginate) || is_int($pageSize)) {
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
     * @return array
     */
    public function getOneCourse(int $id, Carbon $startTime): array
    {
        /** @var Course|Builder $courseBuilder */
        $courseBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
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
                'courses.description',
                'courses.description_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.objectives',
                'courses.objectives_en',
                'courses.contents',
                'courses.contents_en',
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

        /** @var Course $course */
        $course = $courseBuilder->first();

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
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

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
                'courses.description',
                'courses.target_group',
                'courses.objectives',
                'courses.contents',
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
        if ($paginate || $limit) {
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


    /**Filter courses by popular, recent, nearby, skill matching*/
    public function getFilterCourses(array $request, Carbon $startTime, string $type = null): array
    {
        $title = $request['title'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $programId = $request['program_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $curDate = Carbon::now();

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
                'courses.description',
                'courses.description_en',
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

        $coursesBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('programs.row_status', $rowStatus);
            }
        });

        if (is_int($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (!empty($title)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $title . '%');
        }
        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }

        if (is_int($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        if (is_int($programId)) {
            $coursesBuilder->where('courses.program_id', '=', $programId);
        }

        $coursesBuilder->leftJoin("course_enrollments", "courses.id", "=", "course_enrollments.course_id");

        if ($type == self::COURSE_FILTER_POPULAR || $type == self::COURSE_FILTER_RECENT) {
            $coursesBuilder->join("batches", "courses.id", "=", "batches.course_id");
            $coursesBuilder->whereDate('batches.registration_start_date', '<=', $curDate);
            $coursesBuilder->whereDate('batches.registration_end_date', '>=', $curDate);
            if ($type == "recent") {
                $coursesBuilder->orWhereDate('batches.registration_start_date', '>', $curDate);
            }
        }

        $coursesBuilder->groupBy("courses.id");
        $coursesBuilder->orderByDesc('total_enroll');


        /** @var Collection $courses */
        if (is_int($paginate) || is_int($pageSize)) {
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

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {

        if($request['application_form_settings']){
            $request["application_form_settings"] = is_array($request['application_form_settings']) ? $request['application_form_settings'] : explode(',', $request['application_form_settings']);
        }

        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be either 1 or 0'
            ]
        ];

        $rules = [
            'code' => [
                'required',
                'string',
                'max:150',
                'unique:courses,code,' . $id
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id'
            ],

            'branch_id' => [
                'nullable',
                'int',
                'exists:programs,id'
            ],
            'program_id' => [
                'nullable',
                'int',
                'exists:programs,id'
            ],
            'title' => [
                'required',
                'string',
                'max:1000',
                'min:2'
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
            'description' => [
                'nullable',
                'string'
            ],
            'description_en' => [
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
            'contents' => [
                'nullable',
                'string'
            ],
            'contents_en' => [
                'nullable',
                'string'
            ],

            'training_methodology' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'training_methodology_en' => [
                'nullable',
                'string',
                'max: 600',
            ],
            'evaluation_system' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'evaluation_system_en' => [
                'nullable',
                'string',
                'max: 500',
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
                'array',
                'nullable'
            ],
            'application_form_settings.*' => [
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|max:500|min:2',
            'title' => 'nullable|max:1000|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'institute_id' => 'int|gt:0',
            'program_id' => 'nullable|int|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function courseEnrollmentValidator(Request $request, int $id = null): Validator
    {
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'min:1'
            ],
            'first_name' => [
                'required',
                'string',
                'max:300'
            ],
            'first_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'last_name' => [
                'required',
                'string',
                'max:300'
            ],
            'last_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'program_id' => [
                'nullable',
                'exists:programs,id,deleted_at,NULL',
                'int'
            ],
            'course_id' => [
                'required',
                'exists:courses,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'training_center_id' => [
                'required',
                'exists:training_centers,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'batch_id' => [
                'required',
                'exists:batches,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'gender' => [
                'required',
                Rule::in(BaseModel::GENDERS),
                'int',
            ],
            'date_of_birth' => [
                'required',
                'date',
            ],
//            'email' => [
//                'required',
//                'email',
//            ],
//            "mobile" => [
//                "required",
//                "max:11",
//                BaseModel::MOBILE_REGEX
//            ],
            'marital_status' => [
                'required',
                'int',
                Rule::in(CourseEnrollment::MARITAL_STATUSES)
            ],
            'religion' => [
                'required',
                'int',
                Rule::in(CourseEnrollment::RELIGIONS)
            ],
            'nationality' => [
                'int',
                'required'
            ],
            'does_belong_to_ethnic_group' => [
                'int',
                'required'
            ],
            'identity_number_type' => [
                'int',
                'nullable',
                Rule::in(CourseEnrollment::IDENTITY_TYPES)
            ],
            'identity_number' => [
                'string',
                'nullable'
            ],
            'freedom_fighter_status' => [
                'int',
                'required',
                Rule::in(CourseEnrollment::FREEDOM_FIGHTER_STATUSES)
            ],
            'passport_photo_path' => [
                'string',
                'nullable',
            ],
            'signature_image_path' => [
                'string',
                'nullable',
            ],

            'present_address' => [
                'array',
                'required'
            ],
            'present_address.*.loc_division_id' =>[
                'required',
                'integer',
            ],
            'present_address.*.loc_district_id' =>[
                'required',
                'integer',
            ],
            'present_address.*.loc_upazila_id' =>[
                'nullable',
                'integer',
            ],
            'present_address.*.village_or_area' =>[
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'present_address.*.village_or_area_en' =>[
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'present_address.*.house_n_road' =>[
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'present_address.*.house_n_road_en' =>[
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'present_address.*.zip_or_postal_code' =>[
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],

            'permanent_address' => [
                'array',
                'required'
            ],
            'permanent_address.*.loc_division_id' =>[
                'required',
                'integer',
            ],
            'permanent_address.*.loc_district_id' =>[
                'required',
                'integer',
            ],
            'permanent_address.*.loc_upazila_id' =>[
                'nullable',
                'integer',
            ],
            'permanent_address.*.village_or_area' =>[
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'permanent_address.*.village_or_area_en' =>[
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'permanent_address.*.house_n_road' =>[
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'permanent_address.*.house_n_road_en' =>[
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'permanent_address.*.zip_or_postal_code' =>[
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],

            'main_profession' => [
                'required',
                'string',
                'max:500'
            ],
            'main_profession_en' => [
                'required',
                'string',
                'max:250'
            ],
            'other_profession' => [
                'nullable',
                'string',
                'max:500'
            ],
            'other_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'monthly_income' => [
                'required',
                'numeric'
            ],
            'is_currently_employed' => [
                'nullable',
                'int'
            ],
            'years_of_experiences' => [
                'nullable',
                'int'
            ],
            'passing_year' => [
                'nullable',
                'string'
            ],
            'father_name' => [
                'required',
                'string',
                'max:500'
            ],
            'father_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'father_nid' => [
                'nullable',
                'string',
                'max:30'
            ],
            'father_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'father_date_of_birth' => [
                'nullable',
                'date'
            ],
            'mother_name' => [
                'required',
                'string',
                'max:500'
            ],
            'mother_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'mother_nid' => [
                'nullable',
                'string',
                'max:30'
            ],
            'mother_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'mother_date_of_birth' => [
                'nullable',
                'date'
            ],
            'has_own_family_home' => [
                'nullable',
                'int'
            ],
            'has_own_family_land' => [
                'nullable',
                'int'
            ],
            'number_of_siblings' => [
                'nullable',
                'int'
            ],
            'recommended_by_any_organization' => [
                'nullable',
                'int'
            ],

        ];

        if($request['physical_disability_status'] == BaseModel::TRUE){
            $rules['physical_disabilities'] = [
                Rule::requiredIf(function () use ($id, $request) {
                    return $request['physical_disability_status'] == BaseModel::TRUE;
                }),
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($id, $request) {
                    return $request['physical_disability_status'] == BaseModel::TRUE;
                }),
                "exists:physical_disabilities,id,deleted_at,NULL",
                "int",
                "distinct",
                "min:1"
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
