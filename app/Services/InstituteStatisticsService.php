<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class InstituteStatisticsService
{


    public function getTotalCourseEnrollments(int $id = null): int
    {

        $enrollmentBuilder = CourseEnrollment::query();
        if (is_numeric($id)) {
            $enrollmentBuilder->where('institute_id', '=', $id);
        }

        return $enrollmentBuilder->count('id');
    }

    public function getTotalCourses(int $id = null): int
    {
        $courseBuilder = Course::query();
        if (is_numeric($id)) {
            $courseBuilder->where('institute_id', '=', $id);
        }
        return $courseBuilder->count('id');
    }


    /**
     * @param int|null $id
     * @return Collection|array
     */
    public function getDemandedCourses(int $id = null): Collection|array
    {
        /** @var Builder $courseEnrollmentBuilder */
        $courseEnrollmentBuilder = CourseEnrollment::select(DB::raw('count(DISTINCT(course_enrollments.id)) as value , courses.title as name '))
            ->join('courses', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id');
            });

        if (is_numeric($id)) {
            $courseEnrollmentBuilder->where('course_enrollments.institute_id', $id);
        }

        return $courseEnrollmentBuilder->groupby('course_enrollments.course_id')
            ->orderby('value', 'DESC')
            ->limit(6)
            ->get();
    }

    public function getTotalBatches(int $id = null): int
    {
        $batchBuilder = Batch::query();
        if (is_numeric($id)) {
            $batchBuilder->where('institute_id', $id);
        }
        return $batchBuilder->count('id');
    }

    public function getTotalRunningStudents(int $id = null): int
    {
        $currentDate = Carbon::now();

        $batchBuilder = Batch::query();
        if (is_numeric($id)) {
            $batchBuilder->where('institute_id', $id)->whereDate('batch_start_date', '<=', $currentDate)
                ->whereDate('batch_end_date', '>=', $currentDate)->get();
        }

        /** @var Collection $batches */
        $batches = $batchBuilder->get();

        $totalRunningStudent = 0;
        foreach ($batches as $batch) {
            $totalRunningStudent += ($batch->number_of_seats - $batch->available_seats);
        }
        return $totalRunningStudent;
    }

    public function getTotalTrainers(int $id = null): int
    {
        $trainerBuilder = Trainer::query();
        if (is_numeric($id)) {
            $trainerBuilder->where('institute_id', '=', $id);
        }
        return $trainerBuilder->count('id');
    }

    public function getTotalTrainingCenters(int $id = null): int
    {
        $trainingCenterBuilder = TrainingCenter::query();
        if (is_numeric($id)) {
            $trainingCenterBuilder->where('institute_id', $id);
        }
        return $trainingCenterBuilder->count('id');

    }


    public function getTotalDemandFromIndustry(int $id = null): int
    {
        return 0;
    }

    public function getTotalCertificateIssue(int $id = null): int
    {
        return 0;
    }

    public function getTotalTrendingCourse(int $id = null): int
    {
        $courseBuilder = Course::query();
        if (is_numeric($id)) {
            $courseBuilder->where('institute_id', '=', $id);
        }
        return $courseBuilder->count('id');
    }


    public function getDashboardStatisticalData(int $instituteId = null): array
    {
        $dashboardStatData ['total_Enroll'] = $this->getTotalCourseEnrollments($instituteId);
        $dashboardStatData ['total_Course'] = $this->getTotalCourses($instituteId);
        $dashboardStatData ['total_Batch'] = $this->getTotalBatches($instituteId);
        $dashboardStatData ['total_running_students'] = $this->getTotalRunningStudents($instituteId);
        $dashboardStatData ['total_trainers'] = $this->getTotalTrainers($instituteId);
        $dashboardStatData ['total_training_centers'] = $this->getTotalTrainingCenters($instituteId);
        $dashboardStatData ['total_Demand_From_Industry'] = $this->getTotalDemandFromIndustry($instituteId);
        $dashboardStatData ['total_Certificate_Issue'] = $this->getTotalCertificateIssue($instituteId);
        $dashboardStatData ['Total_Trending_Course'] = $this->getTotalTrendingCourse($instituteId);
        return $dashboardStatData;

    }


}
