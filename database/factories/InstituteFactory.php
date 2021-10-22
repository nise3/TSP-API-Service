<?php

namespace Database\Factories;

use App\Models\Institute;
use App\Services\LocationSeederHelper;
use Illuminate\Database\Eloquent\Factories\Factory;


class InstituteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Institute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $address = $this->faker->address;

        $len = count(LocationSeederHelper::$data);
        $index = random_int(0, $len - 1);
        $location = LocationSeederHelper::$data[$index];
        $instituteName = $this->faker->name;
        $headName = $this->faker->name;
        $contactName = $this->faker->name;

        return [
            'title' => $instituteName,
            'title_en' => $instituteName,
            'loc_division_id' => $location['loc_division_id'],
            'loc_district_id' => $location['loc_district_id'],
            'loc_upazila_id' => $location['loc_upazila_id'],
            'location_latitude' => $location['location_longitude'],
            'location_longitude' => $location['location_longitude'],
            'code' => $this->faker->slug(20, false),
            'domain' => 'https://' . $this->faker->domainName,
            'address' => $address,
            'address_en' => $address,
            'primary_phone' => $this->faker->phoneNumber,
            'mobile_numbers' => '["' . $this->faker->numerify('017########') . '","' . $this->faker->numerify('017########') . '"]',
            'primary_mobile' => $this->faker->numerify('017########'),
            'email' => $this->faker->email(),
            'config' => $this->faker->sentence,
            'google_map_src' => $this->faker->sentence,
            'logo' => "softbd.jpg",
            'name_of_the_office_head' => $headName,
            'name_of_the_office_head_en' => $headName,
            'name_of_the_office_head_designation' => 'Principal',
            'name_of_the_office_head_designation_en' => 'Principal',
            'contact_person_name' => $contactName,
            'contact_person_name_en' => $contactName,
            'contact_person_mobile' => $this->faker->numerify('017########'),
            'contact_person_email' => $this->faker->email(),
            'contact_person_designation' => 'HR Manager',
            'contact_person_designation_en' => 'HR Manager',
            'row_status' => 1
        ];
    }

}
