<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
    	return [
            'title_en' => $this->faker->name,
            'title' => $this->faker->name,
            'code' => $this->faker->slug(25, false),
            'description' => $this->faker->paragraph,
            'logo' => "softBd.jpg"
    	];
    }
}
