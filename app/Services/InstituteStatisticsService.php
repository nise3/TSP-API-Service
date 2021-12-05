<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class InstituteStatisticsService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */

    public function getTotalCourseEnrollments(int $id, Carbon $startTime): int
    {
        $Enrollments = CourseEnrollment::where('institute_id', '=', $id)->get();
        $totalCount = $Enrollments->count('id');
        return $totalCount;
    }

    public function getTotalCourses(int $id, Carbon $startTime): int
    {
        $Courses = Course::where('institute_id', '=', $id)->get();
        $totalCount = $Courses->count('id');
        return $totalCount;
    }

    public function DemandingCourses(int $id, Carbon $startTime): array
    {
        return CourseEnrollment::select(DB::raw('count(DISTINCT(course_enrollments.id)) as Value , courses.title as Name '))
            ->join('courses', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id');
            })
            ->where('course_enrollments.institute_id', $id)
            ->groupby('course_enrollments.course_id')
            ->orderby('Value', 'DESC')
            ->limit(6)
            ->get()
            ->toArray();

    }

    public function getTotalBatches(int $id, Carbon $startTime): int
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

    public function getTotalTrainers(int $id, Carbon $startTime): int
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


}
