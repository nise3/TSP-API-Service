<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class InstituteStatisticsService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function instituteIdValidator(array $request) : \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
           'institute_id'=>[
               "required",
               "integer"
           ]
        ];
        return Validator::make($request,$rules);

    }

    public function getTotalCourseEnrollments(int $id): int
    {
        $Enrollments = CourseEnrollment::where('institute_id', '=', $id)->get();
        $totalCount = $Enrollments->count('id');
        return $totalCount;
    }

    public function getTotalCourses(int $id): int
    {
        $Courses = Course::where('institute_id', '=', $id)->get();
        $totalCount = $Courses->count('id');
        return $totalCount;
    }

    public function DemandingCourses(int $id): Collection
    {
        return CourseEnrollment::select(DB::raw('count(DISTINCT(course_enrollments.id)) as Value , courses.title as Name '))
            ->join('courses', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id');
            })
            ->where('course_enrollments.institute_id', $id)
            ->groupby('course_enrollments.course_id')
            ->orderby('Value', 'DESC')
            ->limit(6)
            ->get();

    }

    public function getTotalBatches(int $id): int
    {
        return Batch::where('institute_id', $id)->count('id');
    }

    public function getTotalRunningStudents(int $id): int
    {
        $currentDate = Carbon::now();
//        $authUser = Auth::user();
//        if ($authUser  && $authUser->institute_id) {  //Institute User
//            $id = $authUser->institute_id;
//        }
        /** @var Batch|Builder $batches */

        $batches = Batch::where('institute_id', $id)->whereDate('batch_start_date', '<=', $currentDate)
            ->whereDate('batch_end_date', '>=', $currentDate)->get();
        $totalRunningStudent = 0;
        foreach ($batches as $batch) {
            $totalRunningStudent += ($batch->number_of_seats - $batch->available_seats);
        }
        return $totalRunningStudent;
    }

    public function getTotalTrainers(int $id): int
    {
        $Trainers = Trainer::where('institute_id', '=', $id)->get();
        $totalCount = $Trainers->count('id');
        return $totalCount;
    }

    public function getTotalDemandFromIndustry(int $id)
    {
        return 0;
    }

    public function getTotalCertificateIssue(int $id)
    {
        return 0;
    }

    public function getTotalTrendingCourse(int $id)
    {
        return 0;
    }


    public function getDashboardStatisticalData(int $instituteId):array
    {
        $dashboardStatData ['total_Enroll']  = $this->getTotalCourseEnrollments($instituteId);
        $dashboardStatData ['total_Course']  = $this->getTotalCourses($instituteId);
        $dashboardStatData ['total_Batch']  = $this->getTotalBatches($instituteId);
        $dashboardStatData ['total_running_students']  = $this->getTotalRunningStudents($instituteId);
        $dashboardStatData ['total_trainers']  = $this->getTotalTrainers($instituteId);
        $dashboardStatData ['total_Demand_From_Industry']  = $this->getTotalDemandFromIndustry($instituteId);
        $dashboardStatData ['total_Certificate_Issue']  = $this->getTotalCertificateIssue($instituteId);
        $dashboardStatData ['Total_Trending_Course']  = $this->getTotalTrendingCourse($instituteId);
        return $dashboardStatData;

    }


}
