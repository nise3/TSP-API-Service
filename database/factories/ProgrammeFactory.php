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
            'title_bn' => $this->faker->name,
            'institute_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'code' => $this->faker->unique()->postcode,
            'description' => $this->faker->sentence,
    	];
    }
}
