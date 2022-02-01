<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class InstituteStatisticsService
{


    /**
     * @param int|null $instituteId
     * @return int
     */
    public function getTotalCourseEnrollments(int $instituteId = null): int
    {

        $builder = CourseEnrollment::join("courses", function ($join) {
            $join->on('courses.id', '=', 'course_enrollments.course_id')
                ->whereNull('courses.deleted_at');
        });

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('course_enrollments.' . $queryAttribute, $queryAttributeValue);
        } else {
            $builder->acl();
        }
        return $builder->count('course_enrollments.id');
    }

    /**
     * @param int|null $instituteId
     * @return int
     */
    public function getTotalCourses(int $instituteId = null): int
    {
        $builder = Course::query();

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('courses.' . $queryAttribute, $queryAttributeValue);
        } else {
            $builder->acl();
        }
        return $builder->count('id');
    }

    /**
     * @param int|null $instituteId
     * @return Collection|array
     */
    public function getDemandedCourses(int $instituteId = null): Collection|array
    {
        $builder = CourseEnrollment::select(DB::raw('count(DISTINCT(course_enrollments.id)) as value , courses.title as name '))
            ->join('courses', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id')
                    ->whereNull('courses.deleted_at');
            });

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('course_enrollments.' . $queryAttribute, $queryAttributeValue);
        } else {
            $builder->acl();
        }

        return $builder->groupby('course_enrollments.course_id')
            ->orderby('value', 'DESC')
            ->limit(6)
            ->get();
    }

    /**
     * @param int|null $instituteId
     * @return int
     */
    public function getTotalBatches(int $instituteId = null): int
    {
        $builder = Batch::join('courses', function ($join) {
            $join->on('courses.id', '=', 'batches.course_id')
                ->whereNull('courses.deleted_at');
        });

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('batches.' . $queryAttribute, $queryAttributeValue);
        } else {
            $builder->acl();
        }
        return $builder->count('batches.id');
    }

    /**
     * @param int|null $instituteId
     * @return int
     */
    public function getTotalRunningStudents(int $instituteId = null): int
    {
        $currentDate = Carbon::now();
        $builder = Batch::join('courses', function ($join) {
            $join->on('courses.id', '=', 'batches.course_id')
                ->whereNull('courses.deleted_at');
        })
            ->whereDate('batch_start_date', '<=', $currentDate)
            ->whereDate('batch_end_date', '>=', $currentDate);

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('batches.' . $queryAttribute, $queryAttributeValue);
        } else { // for private auth api
            $builder->acl();
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
     * @return int
     */
    public function getTotalTrainers(int $instituteId = null): int
    {
        $builder = Trainer::query();

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('trainers.' . $queryAttribute, $queryAttributeValue);
        } else { // for private auth api
            $builder->acl();
        }
        return $builder->count('id');
    }

    /**
     * @param int|null $instituteId
     * @return int
     */
    public function getTotalTrainingCenters(int $instituteId = null): int
    {
        $builder = TrainingCenter::query();

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('training_centers.' . $queryAttribute, $queryAttributeValue);
        } else { // for private auth api
            $builder->acl();
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
    public function getTotalTrendingCourse(int $instituteId = null): int
    {
        $builder = Course::query();

        [$queryAttribute, $queryAttributeValue] = self::querySelectorForIndustryAssociationOrInstituteForPublicDomain($instituteId);

        if ($queryAttributeValue) {
            $builder->where('courses.' . $queryAttribute, $queryAttributeValue);
        } else { // for private auth api
            $builder->acl();
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

}
