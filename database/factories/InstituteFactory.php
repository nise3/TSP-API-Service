<?php

namespace Database\Factories;


use App\Models\Institute;
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
     */
    public function definition(): array
    {
        $address = $this->faker->address;

        return [
            'title' => $this->faker->name,
            'title_en' => $this->faker->name,
            'loc_division_id' => "1",
            'loc_district_id' => "1",
            'loc_upazila_id' => "18",
            'code' =>  $this->faker->slug(20, false),
            'domain' => 'https://' . $this->faker->domainName,
            'address' => $address,
            'address_en' => $address,
            'primary_phone' => $this->faker->phoneNumber,
            'mobile_numbers' => "" . $this->faker->numerify('017########') . "," . $this->faker->numerify('017########'),
            'primary_mobile' => $this->faker->numerify('017########'),
            'email' => $this->faker->email(),
            'config' => $this->faker->sentence,
            'google_map_src' => $this->faker->sentence,
            'logo' => "softbd.jpg"
        ];
    }
}
