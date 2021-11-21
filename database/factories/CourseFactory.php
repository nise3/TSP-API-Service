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
            'title' => $this->faker->name,
            'code' => $this->faker->word(),
            'course_fee' => $this->faker->numberBetween(2000, 20000),
            'duration' => $this->faker->numberBetween(8, 72),
            'overview' => $this->faker->paragraph,
            'language_medium' => $this->faker->randomElement([1, 2]),
            'overview_en' => $this->faker->paragraph,
            'target_group' => $this->faker->words(3, true),
            'target_group_en' => $this->faker->words(3, true),
            'objectives' => $this->faker->paragraph,
            'objectives_en' => $this->faker->paragraph,
            'level' => $this->faker->randomElement([1, 2, 3]),
            'lessons' => $this->faker->paragraph,
            'lessons_en' => $this->faker->paragraph,
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
