<?php

namespace Database\Factories;

use App\Models\TrainingCenter;
use App\Services\LocationSeederHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingCenterFactory extends Factory
{
    protected $model = TrainingCenter::class;


    /**
     * @throws \Exception
     */
    public function definition(): array
    {
        $len = count(LocationSeederHelper::$data);
        $index = random_int(0, $len - 1);
        $location = LocationSeederHelper::$data[$index];

        return [
            'title_en' => $this->faker->name,
            'title' => $this->faker->name,
            'loc_division_id' => $location['loc_division_id'],
            'loc_district_id' => $location['loc_district_id'],
            'loc_upazila_id' => $location['loc_upazila_id'],
            'location_latitude' => $location['location_longitude'],
            'location_longitude' => $location['location_longitude'],
            'address' => $this->faker->address,
            'address_en' => $this->faker->address,
            'center_location_type' => $this->faker->randomElement([1, 2, 3]),
            'google_map_src' => $this->faker->address,
        ];
    }
}
