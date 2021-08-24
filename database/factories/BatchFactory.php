<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Programme;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $trainingCenter = TrainingCenter::inRandomOrder()->first();
        $institute = Institute::inRandomOrder()->first();
    	return [
            'training_center_id' => $trainingCenter->id,
            'institute_id' => $institute->id,
            'number_of_seats' => $this->faker->randomDigit(),
            'available_seats' => $this->faker->randomDigit(),
            'in_ethnic_group' => $this->faker->randomElement([0, 1]),
            'is_freedom_fighter' => $this->faker->randomElement([0, 1]),
            'disability_status' => $this->faker->randomElement([0, 1]),
            'ssc_passing_status' => $this->faker->randomElement([0, 1]),
            'hsc_passing_status' => $this->faker->randomElement([0, 1]),
            'masters_passing_status' => $this->faker->randomElement([0, 1]),
            'is_occupation_needed' => $this->faker->randomElement([0, 1]),
            'is_guardian_info_needed' => $this->faker->randomElement([0, 1]),

    	];
    }
}
