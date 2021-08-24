<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Programme;
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
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Institute::query()->truncate();
//        Batch::query()->truncate();
//        TrainingCenter::query()->truncate();
//        Course::query()->truncate();
//        Programme::query()->truncate();
//        Branch::query()->truncate();

        Institute::factory()->count(10)
            ->has(Branch::factory()->count(3))
            ->has(TrainingCenter::factory()->count(3))
            ->has(Programme::factory()->count(3))
            ->has(
                Course::factory()->count(3)->has(Batch::factory()->count(3)
               ))
            ->create();

        Schema::disableForeignKeyConstraints();
    }
}
