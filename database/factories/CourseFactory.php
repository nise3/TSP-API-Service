<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Programme;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title_en' => $this->faker->name,
            'title' => $this->faker->name,
            'code' => $this->faker->slug(30, false),
            'course_fee' => $this->faker->numberBetween(2000, 20000),
            'duration' => $this->faker->numberBetween(8, 72),
            'description' => $this->faker->paragraph,
            'description_en' => $this->faker->paragraph,
            'target_group' => $this->faker->words(3, true),
            'target_group_en' => $this->faker->words(3, true),
            'objectives' => $this->faker->paragraph,
            'objectives_en' => $this->faker->paragraph,
            'contents' => $this->faker->paragraph,
            'contents_en' => $this->faker->paragraph,
            'training_methodology' => $this->faker->sentence,
            'training_methodology_en' => $this->faker->sentence,
            'evaluation_system' => $this->faker->sentence,
            'evaluation_system_en' => $this->faker->sentence,
            'prerequisite' => $this->faker->sentence,
            'prerequisite_en' => $this->faker->sentence,
            'eligibility' => $this->faker->sentence,
            'eligibility_en' => $this->faker->sentence,
            'cover_image' => "softbd.jpg"

        ];
    }

}
