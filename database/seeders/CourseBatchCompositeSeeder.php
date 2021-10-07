<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Program;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CourseBatchCompositeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
//        Schema::disableForeignKeyConstraints();


        try {
            DB::beginTransaction();


            $branches = Branch::select(['institute_id', 'id'])->get()->groupBy('institute_id');
            //       Log::debug($branches->all());
            $trainingCenters = TrainingCenter::select(['institute_id', 'id', 'branch_id'])->get();

            foreach ($trainingCenters as $trainingCenter) {
                if (!$trainingCenter->branch_id && $branches->has($trainingCenter->institute_id)) {
                    $innerBranches = $branches->get($trainingCenter->institute_id);
                    $len = $innerBranches->count();
                    $index = random_int(0, $len - 1);
                    $branchId = $innerBranches->get($index)->id;
                    $trainingCenter->branch_id = $branchId;
                    $trainingCenter->save();
                }
            }


            $programmes = Program::select(['institute_id', 'id'])->get()->groupBy('institute_id');

            $courses = Course::select(['institute_id', 'program_id', 'id'])->get();

            foreach ($courses as $course) {
                if (!$course->program_id && $programmes->has($course->institute_id)) {
                    $innerProgrammes = $programmes->get($course->institute_id);
                    //                  Log::debug($innerProgrammes->toArray());
                    $len = $innerProgrammes->count();
                    $index = random_int(0, $len - 1);
                    $programmeId = $innerProgrammes->get($index)->id;
                    //                   Log::debug($programmeId);
                    $course->program_id = $programmeId;
                    $course->save();
                }
            }

            $trainingCenters = TrainingCenter::select(['id', 'branch_id', 'institute_id'])
                ->get()
                ->groupBy('institute_id');

            $trainers = Trainer::select(['id', 'institute_id'])->get()->groupBy('institute_id');
            $courses = Course::select(['institute_id', 'id'])->get()->keyBy('id');
            $batches = Batch::select(['course_id', 'id'])->get();

            foreach ($batches as $batch) {
                if ($batch->course_id && $courses->has($batch->course_id)) {
                    $course = $courses->get($batch->course_id);
                    $batch->institute_id = $course->institute_id;
                    if ($trainingCenters->has($course->institute_id)) {
                        $innerCenters = $trainingCenters->get($course->institute_id);
                        $len = $innerCenters->count();
                        $index = random_int(0, $len - 1);
                        $centerId = $innerCenters->get($index)->id;
                        $batch->training_center_id = $centerId;
                    }
                    $batch->save();
                    if ($trainers->has($course->institute_id)) {
                        $innerTrainers = $trainers->get($course->institute_id);
                        $len = $innerTrainers->count();
                        $index = random_int(0, $len - 1);
                        $trainerId = $innerTrainers->get($index)->id;
                        $batch->trainers()->attach($trainerId);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
        }


//        Schema::disableForeignKeyConstraints();
    }

}
