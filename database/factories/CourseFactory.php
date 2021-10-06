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
            'course_fee' => $this->faker->randomDigit(),
            'duration' => $this->faker->numberBetween(8, 72),
            'description' => $this->faker->paragraph,
            'target_group' => $this->faker->randomDigit(),
            'objectives' => $this->faker->paragraph,
            'contents' => $this->faker->paragraph,
            'training_methodology' => $this->faker->sentence,
            'evaluation_system' => $this->faker->sentence,
            'prerequisite' => $this->faker->sentence,
            'eligibility' => $this->faker->sentence,
            'cover_image' => "softbd.jpg"

    	];
    }
}
