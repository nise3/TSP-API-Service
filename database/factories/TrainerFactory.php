<?php

namespace Database\Factories;

use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    protected $model = Trainer::class;

    public function definition(): array
    {
        return [
            'trainer_name_en' => $this->faker->name,
            'trainer_name' => $this->faker->name,
            'trainer_registration_number' => $this->faker->uuid(),
            'email' => $this->faker->safeEmail(),
            'mobile' => $this->faker->numerify('017########'),
            'about_me' => $this->faker->sentence,
            'nationality' => 'Bangladeshi',
            'nid' => $this->faker->numerify('#############'),
            'passport_number' => $this->faker->numerify('##################'),
            'gender' => $this->faker->randomElement([1, 2, 3]),
            'marital_status' => $this->faker->randomElement([0, 1]),
            'religion' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'present_address_division_id' => 1,
            'present_address_district_id' => 1,
            'present_address_upazila_id' => 18,
            'present_house_address' => $this->faker->address,
            'present_house_address_en' => $this->faker->address,
            'permanent_address_division_id' => 1,
            'permanent_address_district_id' => 1,
            'permanent_address_upazila_id' => 18,
            'permanent_house_address' => $this->faker->address,
            'permanent_house_address_en' => $this->faker->address,
            'educational_qualification' => $this->faker->sentence,
            'educational_qualification_en' => $this->faker->sentence,
            'skills' => $this->faker->words(6, true),
            'signature' => $this->faker->name,
            'photo' => "softbd.jpg"
        ];
    }
}
