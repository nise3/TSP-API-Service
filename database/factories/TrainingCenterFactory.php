<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingCenterFactory extends Factory
{
    protected $model = TrainingCenter::class;


    public function definition(): array
    {
        $branch = Branch::inRandomOrder()->first();
    	return [
            'title_en' => $this->faker->name,
            'title_bn' => $this->faker->name,
            'branch_id' => $branch->id,
            'address' => $this->faker->address,
    	];
    }
}
