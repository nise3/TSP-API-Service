<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    protected $model = Trainer::class;

    public function definition(): array
    {
        $trainingCenter = TrainingCenter::inRandomOrder()->first();
        $branch = Branch::inRandomOrder()->first();

    	return [
            'training_center_id' => $trainingCenter->id,
            'branch_id' => $branch->id,
            'trainer_name_en' => $this->faker->name,
            'trainer_name_bn' => $this->faker->name,
            'trainer_registration_number' => $this->faker->uuid(),
            'email' => $this->faker->safeEmail(),
            'mobile' => $this->faker->phoneNumber(),
            'about_me' => $this->faker->sentence,
            'nationality' => $this->faker->sentence,
            'nid' => $this->faker->sentence,
            'passport_number' => $this->faker->shuffleString("123456789"),
            'gender' => $this->faker->randomElement([1,2,3]),
            'marital_status' => $this->faker->randomElement([0,1]),
            'religion' => $this->faker->randomElement([1,2,3,4,5]),
            'physical_disabilities_status' => $this->faker->randomElement([0,1]),
            'freedom_fighter_status' => $this->faker->randomElement([0,1]),
            'present_address_division_id' => $this->faker->randomDigit(),
            'present_address_district_id' => $this->faker->randomDigit(),
            'present_house_address' => $this->faker->address,
            'present_address_upazila_id' => $this->faker->address,
            'permanent_address_district_id' => $this->faker->randomDigit(),
            'permanent_address_upazila_id' => $this->faker->randomDigit(),
            'permanent_address_division_id' => $this->faker->randomDigit(),
            'permanent_house_address' => $this->faker->address,
            'educational_qualification' => $this->faker->sentence,
            'skills' => $this->faker->sentence,
            'signature' =>$this->faker->name,
            'photo' =>"softbd.jpg"
    	];
    }
}
