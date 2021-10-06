<?php

namespace Database\Factories;

use App\Models\Programme;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgrammeFactory extends Factory
{
    protected $model = Programme::class;

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
