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
            $institutes = Institute::with(['courses','courses.program', 'courses.batches', 'courses.batches.trainingCenter'])->limit(6)->get();
            foreach ($institutes as $institute){
                $courseEnrollment = new CourseEnrollment();
                $courseEnrollment->institute_id = $institute->id;
                $courseEnrollment->branch_id = $institute->branches[0]->id;
                $courseEnrollment->program_id = $institute->programs[0]->id;
                $courseEnrollment->course_id = $institute->courses[0]->id;
                $courseEnrollment->youth_id = random_int(1, 5);
                $courseEnrollment->save();
            }
        } catch (\Exception $e) {
            throw $e;
        };


        Schema::disableForeignKeyConstraints();
    }
}
