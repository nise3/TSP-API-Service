<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Program;
use App\Models\Skill;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InstituteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $skillIdCollection = Skill::all()->pluck('id');
        $skillIds = $skillIdCollection->toArray();
        $skillIdsLen = count($skillIds);

        Trainer::query()->truncate();
        Batch::query()->truncate();
        Course::query()->truncate();
        Program::query()->truncate();
        TrainingCenter::query()->truncate();
        Branch::query()->truncate();
        Institute::query()->truncate();

        $institutes = Institute::factory()->count(3)->create();

        foreach ($institutes as $institute) {

            /** @var Institute $institute */
            Branch::factory()->state([
                'institute_id' => $institute->id,
            ])->count(3)->create();

            $trainingCenters = TrainingCenter::factory()->state([
                'institute_id' => $institute->id,
            ])->count(6)->create();

            $programs = Program::factory()->state([
                'institute_id' => $institute->id,
            ])->count(2)->create();

            foreach ($programs as $program) {
                $courses = Course::factory()->state([
                    'institute_id' => $institute->id,
                    'program_id' => $program->id
                ])->count(2)->create();

                foreach ($courses as $course) {
                    /** @var Course $course */
                    $index = random_int(0, $skillIdsLen - 1);
                    $skillId = $skillIds[$index];
                    $course->skills()->attach($skillId);
                }
            }

            $courses = Course::all();

            foreach ($trainingCenters as $trainingCenter) {
                /** @var TrainingCenter $trainingCenter */
                $trainers = Trainer::factory()->state([
                    'institute_id' => $institute->id,
                    'training_center_id' => $trainingCenter->id
                ])->count(3)->create();

                $trainerIds = $trainers->pluck('id')->toArray();
                $trainerIdsLen = count($trainerIds);
                foreach ($courses as $course) {
                    /** @var Course $course */
                    /** @var Batch $batch */
                    $batch = Batch::factory()->state([
                        'institute_id' => $institute->id,
                        'training_center_id' => $trainingCenter->id,
                        'course_id' => $course->id
                    ])->create();

                    $index = random_int(0, $trainerIdsLen - 1);
                    $trainerId = $trainerIds[$index];
                    $batch->trainers()->attach($trainerId);
                }

                $trainingCenter->skills()->sync($skillIdCollection->random(3)->toArray());
            }
        }

        Schema::enableForeignKeyConstraints();
    }

}
