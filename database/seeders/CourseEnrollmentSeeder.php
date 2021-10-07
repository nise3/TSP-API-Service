<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Institute;
use App\Models\Program;
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

        try {
            $institutes = Institute::with(['courses', 'courses.program', 'courses.batches', 'courses.batches.trainingCenter'])->limit(6)->get();
            foreach ($institutes as $institute) {
                $courseEnrollment = new CourseEnrollment();
                $courseEnrollment->institute_id = $institute->id;
                $index = random_int(0, $institute->branches->count() - 1);
                $branch = $institute->branches->get($index);
                $courseEnrollment->branch_id = $branch->id;

                $index = random_int(0, $institute->courses->count() - 1);
                $course = $institute->courses->get($index);

                $courseEnrollment->program_id = $course->program_id;
                $courseEnrollment->course_id = $course->id;

                $courseEnrollment->youth_id = random_int(1, 5);
                $courseEnrollment->save();
            }
        } catch (\Exception $e) {
            throw $e;
        };


        Schema::disableForeignKeyConstraints();
    }
}
