<?php

namespace Database\Factories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
    	return [
            'course_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'training_center_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'programme_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'branch_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
    	];
    }
}
