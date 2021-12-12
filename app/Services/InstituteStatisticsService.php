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


    public function getTotalCourseEnrollments(int $instituteId): int
    {
        $builder = CourseEnrollment::join("courses", function ($join) {
            $join->on('courses.id', '=', 'course_enrollments.course_id')
                ->whereNull('courses.deleted_at');
        });

        if ($instituteId) { // from path param in public api
            $builder->where('institute_id', $instituteId);
        } else { // for private auth api
            $builder->acl();
        }
        return $builder->count('course_enrollments.id');
    }

    public function getTotalCourses(int $instituteId): int
    {
        $builder = Course::query();

        if ($instituteId) { // from path param in public api
            $builder->where('institute_id', $instituteId);
        } else { // for private auth api
            $builder->acl();
        }
        return $builder->count('id');
    }

    /**
     * @param int $instituteId
     * @return Collection|array
     */
    public function getDemandedCourses(int $instituteId): Collection|array
    {
        $builder = CourseEnrollment::select(DB::raw('count(DISTINCT(course_enrollments.id)) as value , courses.title as name '))
            ->join('courses', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id')
                    ->whereNull('courses.deleted_at');
            });

        if ($instituteId) { // from path param in public api
            $builder->where('institute_id', $instituteId);
        } else { // for private auth api
            $builder->acl();
        }

        return $builder->groupby('course_enrollments.course_id')
            ->orderby('value', 'DESC')
            ->limit(6)
            ->get();
    }

    public function getTotalBatches(int $instituteId): int
    {
        $builder = Batch::join('courses', function ($join) {
            $join->on('courses.id', '=', 'batches.course_id')
                ->whereNull('courses.deleted_at');
        });

        if ($instituteId) { // from path param in public api
            $builder->where('institute_id', $instituteId);
        } else { // for private auth api
            $builder->acl();
        }
        return $builder->count('batches.id');
    }

    public function getTotalRunningStudents(int $instituteId): int
    {
        $currentDate = Carbon::now();
        $builder = Batch::join('courses', function ($join) {
            $join->on('courses.id', '=', 'batches.course_id')
                ->whereNull('courses.deleted_at');
        })
            ->whereDate('batch_start_date', '<=', $currentDate)
            ->whereDate('batch_end_date', '>=', $currentDate);

        if ($instituteId) { // from path param in public api
            $builder->where('institute_id', $instituteId);
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

    public function getTotalTrainers(): int
    {
        return Trainer::acl()->count('id');
    }

    public function getTotalTrainingCenters(): int
    {
        $trainingCenterBuilder = TrainingCenter::query();
        $trainingCenterBuilder->acl();
        return $trainingCenterBuilder->count('id');

    }


    public function getTotalDemandFromIndustry(): int
    {
        return 0;
    }

    public function getTotalCertificateIssue(): int
    {
        return 0;
    }

    public function getTotalTrendingCourse(): int
    {
        return Course::acl()->count('id');
    }


    public function getDashboardStatisticalData(int $instituteId): array
    {
        $dashboardStatData ['total_enroll'] = $this->getTotalCourseEnrollments($instituteId);
        $dashboardStatData ['total_course'] = $this->getTotalCourses();
        $dashboardStatData ['total_batch'] = $this->getTotalBatches();
        $dashboardStatData ['total_running_students'] = $this->getTotalRunningStudents();
        $dashboardStatData ['total_trainers'] = $this->getTotalTrainers();
        $dashboardStatData ['total_training_centers'] = $this->getTotalTrainingCenters();
        $dashboardStatData ['total_demand_from_industry'] = $this->getTotalDemandFromIndustry();
        $dashboardStatData ['total_certificate_issue'] = $this->getTotalCertificateIssue();
        $dashboardStatData ['total_trending_course'] = $this->getTotalTrendingCourse();
        return $dashboardStatData;

    }

}
