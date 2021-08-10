<?php

namespace Database\Factories;

use App\Models\CourseConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseConfigFactory extends Factory
{
    protected $model = CourseConfig::class;

    public function definition(): array
    {
    	return [
            'institute_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'course_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'training_center_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'programme_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'branch_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
    	];
    }
}
