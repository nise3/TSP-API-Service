<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Services\LocationSeederHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * @throws \Exception
     */
    public function definition(): array
    {
        $address = $this->faker->address;

        $len = count(LocationSeederHelper::$data);
        $index = random_int(0, $len - 1);
        $location = LocationSeederHelper::$data[$index];

        return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'address' => $address,
            'address_en' => $address,
            'loc_division_id' => $location['loc_division_id'],
            'loc_district_id' => $location['loc_district_id'],
            'loc_upazila_id' => $location['loc_upazila_id'],
            'location_latitude' => $location['location_longitude'],
            'location_longitude' => $location['location_longitude'],
            'google_map_src' => $this->faker->address,
        ];
    }
}
