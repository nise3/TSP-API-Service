<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;


class InstituteStatisticsService
{


    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return int
     */
    public function getTotalCourseEnrollments(int $instituteId = null, bool $isNiseStatistics = false): int
    {

        $builder = CourseEnrollment::join("courses", function ($join) {
            $join->on('courses.id', '=', 'course_enrollments.course_id')
                ->whereNull('courses.deleted_at');
        });

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

            if ($queryAttributeValue) {
                $builder->where('course_enrollments.' . $queryAttribute, $queryAttributeValue);
            } else {
                $builder->acl();
            }
        }

        return $builder->count('course_enrollments.id');
    }

    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return int
     */
    public function getTotalCourses(int $instituteId = null, bool $isNiseStatistics = false): int
    {
        $builder = Course::query();

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

            if ($queryAttributeValue) {
                $builder->where('courses.' . $queryAttribute, $queryAttributeValue);
            } else {
                $builder->acl();
            }
        }
        return $builder->count('id');
    }

    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return Collection|array
     */
    public function getDemandedCourses(int $instituteId = null, bool $isNiseStatistics = false): Collection|array
    {
        $builder = CourseEnrollment::select(DB::raw('count(DISTINCT(course_enrollments.id)) as value , courses.title as name '))
            ->join('courses', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id')
                    ->whereNull('courses.deleted_at');
            });

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

            if ($queryAttributeValue) {
                $builder->where('course_enrollments.' . $queryAttribute, $queryAttributeValue);
            } else {
                $builder->acl();
            }
        }

        return $builder->groupby('course_enrollments.course_id')
            ->orderby('value', 'DESC')
            ->limit(6)
            ->get();
    }

    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return int
     */
    public function getTotalBatches(int $instituteId = null, bool $isNiseStatistics = false): int
    {
        $builder = Batch::join('courses', function ($join) {
            $join->on('courses.id', '=', 'batches.course_id')
                ->whereNull('courses.deleted_at');
        });

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);
            if ($queryAttributeValue) {
                $builder->where('batches.' . $queryAttribute, $queryAttributeValue);
            } else {
                $builder->acl();
            }
        }
        return $builder->count('batches.id');
    }

    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return int
     */
    public function getTotalRunningStudents(int $instituteId = null, bool $isNiseStatistics = false): int
    {
        $currentDate = Carbon::now();
        $builder = Batch::join('courses', function ($join) {
            $join->on('courses.id', '=', 'batches.course_id')
                ->whereNull('courses.deleted_at');
        })
            ->whereDate('batch_start_date', '<=', $currentDate)
            ->whereDate('batch_end_date', '>=', $currentDate);

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);
            if ($queryAttributeValue) {
                $builder->where('batches.' . $queryAttribute, $queryAttributeValue);
            } else { // for private auth api
                $builder->acl();
            }
        }
        $batches = $builder->get();

        $totalRunningStudent = 0;
        foreach ($batches as $batch) {
            $totalRunningStudent += ($batch->number_of_seats - $batch->available_seats);
        }
        return $totalRunningStudent;
    }

    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return int
     */
    public function getTotalTrainers(int $instituteId = null, bool $isNiseStatistics = false): int
    {
        $authUser = Auth::user();

        $trainerBuilder = Trainer::query();
        $trainerBuilder->leftJoin('institute_trainers', 'trainers.id', '=', 'institute_trainers.trainer_id');

        /**instituteId will be provided from domain for public dashboard statistics**/
        if (!empty($instituteId)) {
            $trainerBuilder->where('institute_trainers.institute_id', $instituteId);
        }

        if ($authUser && $authUser->isInstituteUser()) {
            $trainerBuilder->where('institute_trainers.institute_id', $authUser->institute_id);
        } else if ($authUser && $authUser->isIndustryAssociationUser()) {
            $trainerBuilder->where('institute_trainers.industry_association_id', $authUser->industry_association_id);
        }

        return $trainerBuilder->count('trainers.id');
    }

    /**
     * @param int|null $instituteId
     * @param bool $isNiseStatistics
     * @return int
     */
    public function getTotalTrainingCenters(int $instituteId = null, bool $isNiseStatistics = false): int
    {
        $builder = TrainingCenter::query();

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);
            if ($queryAttributeValue) {
                $builder->where('training_centers.' . $queryAttribute, $queryAttributeValue);
            } else { // for private auth api
                $builder->acl();
            }
        }
        return $builder->count('id');

    }


    /**
     * @return int
     */
    public function getTotalDemandFromIndustry(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getTotalCertificateIssue(): int
    {
        return 0;
    }

    /**
     * @param int|null $instituteId
     * @return int
     */
    public function getTotalTrendingCourse(int $instituteId = null, bool $isNiseStatistics = false): int
    {
        $builder = Course::query();
        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

            if ($queryAttributeValue) {
                $builder->where('courses.' . $queryAttribute, $queryAttributeValue);
            } else { // for private auth api
                $builder->acl();
            }
        }
        return $builder->count('id');
    }


    /**
     * @param int|null $instituteId
     * @return array
     */
    public function getDashboardStatisticalData(int $instituteId = null): array
    {
        $dashboardStatData ['total_enroll'] = $this->getTotalCourseEnrollments($instituteId);
        $dashboardStatData ['total_course'] = $this->getTotalCourses($instituteId);
        $dashboardStatData ['total_batch'] = $this->getTotalBatches($instituteId);
        $dashboardStatData ['total_running_students'] = $this->getTotalRunningStudents($instituteId);
        $dashboardStatData ['total_trainers'] = $this->getTotalTrainers($instituteId);
        $dashboardStatData ['total_training_centers'] = $this->getTotalTrainingCenters($instituteId);
        $dashboardStatData ['total_demand_from_industry'] = $this->getTotalDemandFromIndustry();
        $dashboardStatData ['total_certificate_issue'] = $this->getTotalCertificateIssue();
        $dashboardStatData ['total_trending_course'] = $this->getTotalTrendingCourse($instituteId);

        return $dashboardStatData;
    }

    private function getPopularCoursesWithEnrollments(bool $isNiseStatistics = false): array
    {
        $builder = Course::query();
        $builder->select([
            "courses.title as course_title",
            "courses.title_en as course_title_en"
        ]);

        $builder->selectRaw('COUNT(course_enrollments.id) AS total_enrollments');
        $builder->join('course_enrollments', 'course_enrollments.course_id', "courses.id");
        $builder->groupBy('course_enrollments.course_id');
        $builder->orderBy('total_enrollments', "DESC");

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain();
            if ($queryAttributeValue) {
                $builder->where('courses.' . $queryAttribute, $queryAttributeValue);
            } else { // for private auth api
                $builder->acl();
            }
        }

        return $builder->limit(4)->get()->toArray();
    }

    #[ArrayShape(["total_popular_courses" => "array", "total_skill_development_center" => "array"])]
    public function getNiseStatistics(): array
    {
        return [
            "total_popular_courses" => $this->getPopularCoursesWithEnrollments(true),
            "total_skill_development_center" => $this->getTotalTrainingCenterWithTrained(true)
        ];
    }

    private static function querySelectorForIndustryAssociationOrInstituteForPublicDomain(int $instituteId = null): array
    {

        $queryAttribute = "institute_id";
        $queryAttributeValue = null;

        if (request()->has('industry_association_id')) {
            $queryAttribute = "industry_association_id";
            $queryAttributeValue = request()->offsetGet('industry_association_id');
        } else if (request()->has('institute_id') || $instituteId) {
            $queryAttributeValue = !empty($instituteId) ? $instituteId : request()->offsetGet('institute_id');
        }

        return [
            $queryAttribute,
            $queryAttributeValue
        ];
    }

    private function getTotalTrainingCenterWithTrained(bool $isNiseStatistics = false): array
    {
        $builder = CourseEnrollment::query();
        $builder->select([
            "training_centers.title as training_center_title",
            "training_centers.title_en as training_center_title_en",
        ]);

        $builder->selectRaw('COUNT(course_enrollments.id) AS total_trained');
        $builder->join('training_centers', 'course_enrollments.training_center_id', "training_centers.id");
        $builder->groupBy('course_enrollments.training_center_id');
        $builder->orderBy('total_trained', "DESC");

        if (!$isNiseStatistics) /** It invokes in time of institute wise statistics */ {
            [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain();
            if ($queryAttributeValue) {
                $builder->where('courses.' . $queryAttribute, $queryAttributeValue);
            } else { // for private auth api
                $builder->acl();
            }
        }

        return $builder->limit(4)->get()->toArray();
    }

}
