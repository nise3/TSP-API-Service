<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $address = $this->faker->address;
        return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'address' => $address,
            'address_en' => $address,
            'loc_division_id' => "1",
            'loc_district_id' => "1",
            'loc_upazila_id' => "18",
            'google_map_src' => $this->faker->address,
        ];
    }
}
