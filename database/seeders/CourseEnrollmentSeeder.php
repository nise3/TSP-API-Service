<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Database\Seeder;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courses = Course::all();
        foreach ($courses as $course) {
            for ($i = 1; $i < 21; $i++) {
                $courseEnrollment = new CourseEnrollment();
                $courseEnrollment->course_id = $course->id;
                $courseEnrollment->youth_id = $i;
                $courseEnrollment->save();
            }
        }

    }
}
