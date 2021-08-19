<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
    	return [
            'title_en' => $this->faker->name,
            'title_bn' => $this->faker->name,
            'code' => $this->faker->unique()->countryCode,
            'course_fee' => $this->faker->randomElement([0,200,399,400,566,666,722,811,911,1110]),
            'duration' => $this->faker->time,
            'description' => $this->faker->sentence,

    	];
    }
}
