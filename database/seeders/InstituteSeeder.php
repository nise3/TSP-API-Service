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
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\InstituteService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class InstituteSeeder extends Seeder
{
    const createInstitute = true;
    const createIdpUser = false;

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function run()
    {
        /** @var InstituteService $instituteService */
        $instituteService = app(InstituteService::class);

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

        if (self::createInstitute) {
            $institutes = Institute::factory()->count(3)->create();
        } else {
            $institutes = Institute::all();
        }

        foreach ($institutes as $institute) {

            /** @var Institute $institute */
            if (self::createIdpUser) {
                try {
                    $instituteData = $institute->toArray();
                    unset($instituteData['id']);
                    $instituteData['permission_sub_group_id'] = 5;
                    $instituteData['institute_id'] = $institute->id;

                    $instituteService->createUser($instituteData);
                } catch (\Exception $e) {
                    Log::debug('User Creation Failed for Institute id: ', $institute->id);
                    Log::debug($e->getCode() . ' - ' . $e->getMessage());
                }
            }

            Branch::factory()
                ->state([
                    'institute_id' => $institute->id,
                    "code" => CodeGeneratorService::getBranchCode($institute->id)
                ])
                ->count(1)
                ->create();

            $trainingCenters = TrainingCenter::factory()
                ->state([
                    'institute_id' => $institute->id,
                    'code' => CodeGeneratorService::getTrainingCenterCode($institute->id)
                ])
                ->count(1)
                ->create();

            $programs = Program::factory()
                ->state([
                    'institute_id' => $institute->id,
                ])
                ->count(1)
                ->create();

            foreach ($programs as $program) {
                $courses = Course::factory()
                    ->state([
                        'institute_id' => $institute->id,
                        'program_id' => $program->id,
                        "code" => CodeGeneratorService::getCourseCode($program->institute_id)
                    ])
                    ->count(1)
                    ->create();

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
                ])->count(1)->create();

                $trainerIds = $trainers->pluck('id')->toArray();
                $trainerIdsLen = count($trainerIds);
                foreach ($courses as $course) {
                    /** @var Course $course */
                    /** @var Batch $batch */
                    $batch = Batch::factory()->state([
                        'institute_id' => $institute->id,
                        'training_center_id' => $trainingCenter->id,
                        'course_id' => $course->id,
                        "code" => CodeGeneratorService::getBatchCode($course->id)
                    ])->create();

                    $index = random_int(0, $trainerIdsLen - 1);
                    $trainerId = $trainerIds[$index];
                    $batch->trainers()->attach($trainerId);
                }

                $trainingCenter->skills()->sync($skillIdCollection->random(1)->toArray());
            }
        }

        Schema::enableForeignKeyConstraints();
    }

}
