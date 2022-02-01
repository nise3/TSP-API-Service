<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Institute;
use App\Models\Skill;
use App\Models\Trainer;
use Carbon\Carbon;
use Faker\Provider\Base;
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
                'courses.industry_association_id',
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
        )->acl();

        $coursesBuilder->leftJoin("institutes", function ($join) use ($rowStatus) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $coursesBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('courses.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
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
     * @param bool $withTrainers
     * @return Course
     */
    public function getOneCourse(int $id, bool $withTrainers = false): Course
    {
        $youthId = request('youth_id') ?: "";
        $curDate = Carbon::now();

        /** @var Course|Builder $courseBuilder */
        $courseBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.level',
                'courses.language_medium',
                'courses.institute_id',
                'courses.industry_association_id',
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
                'courses.deleted_at'
            ]
        );

        $courseBuilder->leftJoin("institutes", function ($join) {
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

        /** @var CourseEnrollment|Builder $courseEnrolled */
        $course["enroll_count"] = CourseEnrollment::where('course_id', $course->id)->count();

        if ($withTrainers == true) {
            /** @var Builder $trainerBuilder */
            $trainerBuilder = Trainer::select([
                'trainers.trainer_name',
                'trainers.trainer_name_en',
                'trainers.email',
                'trainers.mobile',
                'trainers.photo',
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

        if (is_numeric($youthId)) {
            $courseEnrollment = CourseEnrollment::where('course_id', $id)->where('youth_id', $youthId)->first();
            $course["enrolled"] = (bool)$courseEnrollment;
        }

        /** Set enrollable field to determine weather Youth Can Enroll into this course */
        /** @var Batch | Builder $batch */
        $batch = Batch::where('course_id', $course->id);
        $batch->where(function ($builder) use ($curDate) {
            $builder->whereDate('batches.registration_start_date', '<=', $curDate);
            $builder->whereDate('batches.registration_end_date', '>=', $curDate);
        });
        $batchCount = $batch->count();
        $course["enrollable"] = $batchCount > 0;

        return $course;
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

        if (!empty($data["skills"])) {
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

    // TODO: Bellow method should refactor after production phase-2
    /** Filter courses by popular, recent, nearby, skill matching
     * @param array $request
     * @param Carbon $startTime
     * @param string|null $type
     * @return array
     */
//    public function getFilterCourses(array $request, Carbon $startTime, string $type = null): array
//    {
//        $pageSize = $request['page_size'] ?? "";
//        $paginate = $request['page'] ?? "";
//        $rowStatus = $request['row_status'] ?? "";
//        $curDate = Carbon::now();
//
//        /** Filters variables */
//        $searchText = $request['search_text'] ?? "";
//        $locDistrictId = $request['loc_district_id'] ?? "";
//        $locUpazilaId = $request['loc_upazila_id'] ?? "";
//        $skillIds = $request['skill_ids'] ?? [];
//        $instituteId = $request['institute_id'] ?? "";
//        $programId = $request['program_id'] ?? "";
//        $availability = $request['availability'] ?? "";
//        $language = $request['language_medium'] ?? "";
//        $courseType = $request['course_type'] ?? "";
//        $courseName = $request['course_name'] ?? "";
//        $courseLevel = $request['level'] ?? "";
//        $youthId = $request['youth_id'] ?? "";
//
//        /** @var Course|Builder $coursesBuilder */
//        $coursesBuilder = Course::select(
//            [
//                'courses.id',
//                'courses.code',
//                'courses.title_en',
//                'courses.title',
//                'courses.institute_id',
//                'institutes.title as institute_title',
//                'institutes.title_en as institute_title_en',
//                'courses.program_id',
//                'programs.title as program_title',
//                'programs.title_en as program_title_en',
//                'courses.course_fee',
//                'courses.duration',
//                'courses.overview',
//                'courses.overview_en',
//                'courses.target_group',
//                'courses.target_group_en',
//                'courses.prerequisite',
//                'courses.prerequisite_en',
//                'courses.eligibility',
//                'courses.eligibility_en',
//                'courses.cover_image',
//                'courses.row_status',
//                'courses.created_by',
//                'courses.updated_by',
//                'courses.created_at',
//                'courses.updated_at',
//                'courses.deleted_at',
//                DB::raw('COUNT(distinct course_enrollments.id) as total_enroll')
//            ]
//        );
//
//        $coursesBuilder->join("institutes", function ($join) use ($rowStatus, $instituteId) {
//            $join->on('courses.institute_id', '=', 'institutes.id')
//                ->whereNull('institutes.deleted_at');
//        });
//
//        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus, $programId) {
//            $join->on('courses.program_id', '=', 'programs.id')
//                ->whereNull('programs.deleted_at');
//        });
//
//        $coursesBuilder->leftJoin("course_enrollments", "courses.id", "=", "course_enrollments.course_id");
//        $coursesBuilder->join("batches", "courses.id", "=", "batches.course_id");
//
//        if (!empty($searchText) || !empty($locUpazilaId) || !empty($locDistrictId) ||
//            ($type == self::COURSE_FILTER_NEARBY) || ($type == self::COURSE_FILTER_SKILL_MATCHING)) {
//            $coursesBuilder->join('training_centers', 'training_centers.id', '=', 'batches.training_center_id');
//            $coursesBuilder->join('course_skill', 'course_skill.course_id', '=', 'courses.id');
//
//            /** Search courses by search_text */
//            if (!empty($searchText)) {
//                $coursesBuilder->leftJoin('loc_divisions', 'loc_divisions.id', 'training_centers.loc_division_id');
//                $coursesBuilder->leftJoin('loc_districts', 'loc_districts.id', 'training_centers.loc_district_id');
//                $coursesBuilder->leftJoin('loc_upazilas', 'loc_upazilas.id', 'training_centers.loc_upazila_id');
//                $coursesBuilder->leftJoin('skills', 'skills.id', 'course_skill.skill_id');
//
//                $coursesBuilder->where(function ($builder) use ($searchText) {
//                    $builder->orWhere('courses.title', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('courses.title_en', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('loc_divisions.title', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('loc_divisions.title_en', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('loc_districts.title', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('loc_districts.title_en', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('loc_upazilas.title', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('loc_upazilas.title_en', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('skills.title', 'like', '%' . $searchText . '%');
//                    $builder->orWhere('skills.title_en', 'like', '%' . $searchText . '%');
//                });
//            }
//
//            if ($type == self::COURSE_FILTER_NEARBY || !empty($locUpazilaId) || !empty($locDistrictId)) {
//                if ($locUpazilaId) {
//                    $coursesBuilder->where('training_centers.loc_upazila_id', '=', $locUpazilaId);
//                } else if ($locDistrictId) {
//                    $coursesBuilder->where('training_centers.loc_district_id', '=', $locDistrictId);
//                }
//            }
//
//            if ($type == self::COURSE_FILTER_SKILL_MATCHING) {
//                if (is_array($skillIds) && count($skillIds) > 0) {
//                    $coursesBuilder->whereIn('course_skill.skill_id', $skillIds);
//                }
//            }
//        }
//
//        if (is_numeric($rowStatus)) {
//            $coursesBuilder->where('courses.row_status', $rowStatus);
//        }
//
//        if (is_numeric($instituteId)) {
//            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
//        }
//
//        if (is_numeric($programId)) {
//            $coursesBuilder->where('courses.program_id', '=', $programId);
//        }
//
//        if (is_numeric($language)) {
//            $coursesBuilder->where('courses.language_medium', '=', $language);
//        }
//
//        if (is_numeric($courseType)) {
//            if ($courseType == self::COURSE_FILTER_COURSE_TYPE_PAID) {
//                $coursesBuilder->where('courses.course_fee', '!=', 0);
//                $coursesBuilder->Where('courses.course_fee', '!=', null);
//            } else if ($courseType == self::COURSE_FILTER_COURSE_TYPE_FREE) {
//                $coursesBuilder->where('courses.course_fee', '=', 0);
//                $coursesBuilder->orWhere('courses.course_fee', '=', null);
//            }
//        }
//
//        if (!empty($courseName)) {
//            $coursesBuilder->where('courses.title', 'like', '%' . $courseName . '%');
//            $coursesBuilder->orWhere('courses.title_en', 'like', '%' . $courseName . '%');
//        }
//
//        if (is_numeric($courseLevel)) {
//            $coursesBuilder->where('courses.level', '=', $courseLevel);
//        }
//
//        if ($type == self::COURSE_FILTER_POPULAR || $type == self::COURSE_FILTER_RECENT || is_numeric($availability)) {
//            if ($type == self::COURSE_FILTER_POPULAR || $availability == self::COURSE_FILTER_AVAILABILITY_RUNNING || $type == self::COURSE_FILTER_RECENT) {
//                $coursesBuilder->where(function($builder) use($curDate){
//                    $builder->whereDate('batches.registration_start_date', '<=', $curDate);
//                    $builder->whereDate('batches.registration_end_date', '>=', $curDate);
//                });
//            }
//            if ($type != self::COURSE_FILTER_POPULAR && $availability == self::COURSE_FILTER_AVAILABILITY_UPCOMING) {
//                $coursesBuilder->WhereDate('batches.registration_start_date', '>', $curDate);
//            }
//            if ($availability == self::COURSE_FILTER_AVAILABILITY_COMPLETED) {
//                $coursesBuilder->whereDate('batches.batch_end_date', '<', $curDate);
//            }
//        }
//
//        if ($type == self::COURSE_FILTER_TRENDING) {
//            $coursesBuilder->inRandomOrder()->limit(10);
//        }
//
//        $coursesBuilder->groupBy("courses.id");
//        $coursesBuilder->orderByDesc('total_enroll');
//
//
//        /** @var Collection $courses */
//        if (is_numeric($paginate) || is_numeric($pageSize)) {
//            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
//            $courses = $coursesBuilder->paginate($pageSize);
//            $paginateData = (object)$courses->toArray();
//            $response['current_page'] = $paginateData->current_page;
//            $response['total_page'] = $paginateData->last_page;
//            $response['page_size'] = $paginateData->per_page;
//            $response['total'] = $paginateData->total;
//
//        }
//        else if ($type == self::COURSE_FILTER_POPULAR || $type == self::COURSE_FILTER_RECENT) {
//            $courses = $coursesBuilder->get()->take(20);
//        }
//        else {
//            $courses = $coursesBuilder->get();
//        }
//
//        /** Set course already enrolled OR not for youth */
//        if(is_numeric($youthId)){
//            $courseIds = $courses->pluck('id')->toArray();
//            if(count($courseIds) > 0){
//                $youthEnrolledCourseIds = CourseEnrollment::whereIn('course_id', $courseIds)
//                    ->where('youth_id', $youthId)
//                    ->pluck('course_id')
//                    ->toArray();
//
//                foreach ($courses as $course){
//                    $course['enrolled'] = (bool) in_array($course->id, $youthEnrolledCourseIds);
//                }
//            }
//        }
//
//        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
//        $response['_response_status'] = [
//            "success" => true,
//            "code" => Response::HTTP_OK,
//            "query_time" => $startTime->diffInSeconds(Carbon::now()),
//        ];
//
//        return $response;
//    }

    // TODO: Bellow method should remove after Production Phase-v2
    public function getFilterCourses(array $request, Carbon $startTime, string $type = null): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $curDate = Carbon::now();

        /** Filters variables */
        $searchText = $request['search_text'] ?? "";
        $locDistrictId = $request['loc_district_id'] ?? "";
        $locUpazilaId = $request['loc_upazila_id'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $instituteId = $request['institute_id'] ?? "";
        $industryAssociationId = $request['industry_association_id'] ?? "";
        $programId = $request['program_id'] ?? "";
        $availability = $request['availability'] ?? "";
        $language = $request['language_medium'] ?? "";
        $courseType = $request['course_type'] ?? "";
        $courseName = $request['course_name'] ?? "";
        $courseLevel = $request['level'] ?? "";
        $youthId = $request['youth_id'] ?? "";

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.title_en',
                'courses.title',
                'courses.institute_id',
                'courses.industry_association_id',
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

        $coursesBuilder->leftJoin("institutes", function ($join) use ($rowStatus, $instituteId) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus, $programId) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
        });

        $coursesBuilder->leftJoin("course_enrollments", "courses.id", "=", "course_enrollments.course_id");
        $coursesBuilder->leftJoin("batches", "courses.id", "=", "batches.course_id");

        if (!empty($searchText) || !empty($locUpazilaId) || !empty($locDistrictId)) {
            $coursesBuilder->join('training_centers', 'training_centers.id', '=', 'batches.training_center_id');
            $coursesBuilder->join('course_skill', 'course_skill.course_id', '=', 'courses.id');

            /** Search courses by search_text */
            if (!empty($searchText)) {
                $coursesBuilder->leftJoin('loc_divisions', 'loc_divisions.id', 'training_centers.loc_division_id');
                $coursesBuilder->leftJoin('loc_districts', 'loc_districts.id', 'training_centers.loc_district_id');
                $coursesBuilder->leftJoin('loc_upazilas', 'loc_upazilas.id', 'training_centers.loc_upazila_id');
                $coursesBuilder->leftJoin('skills', 'skills.id', 'course_skill.skill_id');

                $coursesBuilder->where(function ($builder) use ($searchText) {
                    $builder->orWhere('courses.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('courses.title_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_divisions.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_divisions.title_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_districts.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_districts.title_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_upazilas.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('loc_upazilas.title_en', 'like', '%' . $searchText . '%');
                    $builder->orWhere('skills.title', 'like', '%' . $searchText . '%');
                    $builder->orWhere('skills.title_en', 'like', '%' . $searchText . '%');
                });
            }
        }

        if (is_numeric($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (is_numeric($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        if (is_numeric($industryAssociationId)) {
            $coursesBuilder->where('courses.industry_association_id', '=', $industryAssociationId);
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

        if ($type == self::COURSE_FILTER_RECENT || is_numeric($availability)) {
            if ($type == self::COURSE_FILTER_RECENT || $availability == self::COURSE_FILTER_AVAILABILITY_RUNNING) {
                $coursesBuilder->where(function ($builder) use ($curDate) {
                    $builder->whereDate('batches.registration_start_date', '<=', $curDate);
                    $builder->whereDate('batches.registration_end_date', '>=', $curDate);
                    $builder->whereNull('batches.deleted_at');
                });
            }
            if ($availability == self::COURSE_FILTER_AVAILABILITY_UPCOMING) {
                $coursesBuilder->WhereDate('batches.registration_start_date', '>', $curDate);
                $coursesBuilder->whereNull('batches.deleted_at');
            }
            if ($availability == self::COURSE_FILTER_AVAILABILITY_COMPLETED) {
                $coursesBuilder->whereDate('batches.batch_end_date', '<', $curDate);
                $coursesBuilder->whereNull('batches.deleted_at');
            }
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

        } else {
            $courses = $coursesBuilder->get();
        }

        /** Set course already enrolled OR not for youth */
        if (is_numeric($youthId)) {
            $courseIds = $courses->pluck('id')->toArray();
            if (count($courseIds) > 0) {
                $youthEnrolledCourseIds = CourseEnrollment::whereIn('course_id', $courseIds)
                    ->where('youth_id', $youthId)
                    ->pluck('course_id')
                    ->toArray();

                foreach ($courses as $course) {
                    $course['enrolled'] = (bool)in_array($course->id, $youthEnrolledCourseIds);
                }
            }
        }

        /** Set enrollable field in course */
        /** @var $batchBuilder $batchBuilder */
        $onGoingRegCourseIds = Batch::where(function ($builder) use ($curDate) {
            $builder->whereDate('batches.registration_start_date', '<=', $curDate);
            $builder->whereDate('batches.registration_end_date', '>=', $curDate);
        })->pluck('course_id')->toArray();

        foreach ($courses as $course) {
            $course['enrollable'] = (bool)in_array($course->id, $onGoingRegCourseIds);
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

    public function getCourseCount(): int
    {
        return Course::count('id');
    }

    public function getSkillMatchingCourseCount(array $skillIds): int
    {
        return DB::table('course_skill')
            ->join('courses', function ($join) {
                $join->on('courses.id', 'course_skill.course_id')
                    ->whereNull('courses.deleted_at');
            })
            ->whereIn('skill_id', $skillIds)
            ->count('course_skill.course_id');
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
                'sometimes',
                'required',
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

        $rules = array_merge(BaseModel::industryOrIndustryAssociationValidationRules(), $rules);

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
            'program_id' => 'nullable|int|gt:0',
            'course_name' => 'nullable|string',
            'search_text' => 'nullable|string|min:2',
            'loc_district_id' => 'nullable|int|gt:0',
            'loc_upazila_id' => 'nullable|int|gt:0',
            'youth_id' => 'nullable|int|gt:0'
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

        $rules = array_merge(BaseModel::industryOrIndustryAssociationValidationRulesForFilter(), $rules);

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCourseTitle(Request $request): array
    {
        /** @var Course|Builder $batchBuilder */
        $courseBuilder = Course::select([
            'id',
            'title',
            'title_en'
        ]);

        if ($request->filled('course_ids') && is_array($request->input('course_ids'))) {
            $courseBuilder->whereIn("id", $request->input('course_ids'));
        }

        return $courseBuilder->get()->keyBy("id")->toArray();
    }
}
