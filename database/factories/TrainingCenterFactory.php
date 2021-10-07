<?php

namespace Database\Factories;

use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingCenterFactory extends Factory
{
    protected $model = TrainingCenter::class;


    public function definition(): array
    {
        return [
            'title_en' => $this->faker->name,
            'title' => $this->faker->name,
            'loc_division_id' => "1",
            'loc_district_id' => "1",
            'loc_upazila_id' => "18",
            'address' => $this->faker->address,
            'address_en' => $this->faker->address,
            'center_location_type' => $this->faker->randomElement([1, 2, 3]),
        ];
    }
}
