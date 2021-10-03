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
        return [
            'title_en' => $this->faker->name,
            'title_bn' => $this->faker->name,
            'loc_division_id' => "1",
            'loc_district_id' =>"1",
            'loc_upazila_id' => "18",
            'code' => $this->faker->unique()->countryCode,
            'domain' => 'http://' . $this->faker->domainName,
            'address' => $this->faker->address,
            'primary_phone' => $this->faker->phoneNumber,
            'phone_numbers' => $this->faker->phoneNumber,
            'primary_mobile' => $this->faker->phoneNumber,
            'email' => $this->faker->email(),
            'config' => $this->faker->sentence,
            'google_map_src' => $this->faker->sentence,
            'logo' => "softbd.jpg"
        ];
    }
}
