<?php

namespace Database\Factories;

use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingCenterFactory extends Factory
{
    protected $model = TrainingCenter::class;

    public function definition(): array
    {
    	return [
            'title_en' => $this->faker->name,
            'title_bn' => $this->faker->name,
            'institute_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
//            'branch_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'address' => $this->faker->address,
    	];
    }
}
