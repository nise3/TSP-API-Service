<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Programme;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $trainingCenter = TrainingCenter::inRandomOrder()->first();
        $programmeId = Programme::inRandomOrder()->first();
        $instituteId = Institute::inRandomOrder()->first();
        $branchId = Branch::inRandomOrder()->first();
        return [
            'training_center_id' => $trainingCenter->id,
            'programme_id' => $programmeId,
            'number_of_seats' => $this->faker->numberBetween(30,100),
            'institute_id' => $instituteId,
            'branch_id' => $branchId,
            'registration_start_date' => $this->faker->date('2021-7-21'),
            'registration_end_date' => $this->faker->date('2021-8-11'),
            'batch_start_date' => $this->faker->date('2021-8-21'),
            'batch_end_date' => $this->faker->date('2021-11-21')
        ];
    }
}
