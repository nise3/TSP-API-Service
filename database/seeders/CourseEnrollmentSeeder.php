<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        CourseEnrollment::query()->truncate();
        $courses = Course::all();
        foreach ($courses as $course) {
            for ($i = 1; $i < 21; $i++) {
                $courseEnrollment = app(CourseEnrollment::class);
                $courseEnrollment->institute_id = $course->institute_id;
                $courseEnrollment->course_id = $course->id;
                $courseEnrollment->program_id = $course->program_id;
                $courseEnrollment->youth_id = $i;
                $courseEnrollment->save();
            }
        }
        Schema::disableForeignKeyConstraints();
    }
}
