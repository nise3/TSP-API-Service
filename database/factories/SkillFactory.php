<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $title = $this->faker->unique->jobTitle;
        return [
            'title_en' => ucfirst($title),
            'title' => ucfirst($title)
        ];
    }
}
