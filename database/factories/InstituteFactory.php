<?php

namespace Database\Factories;


use App\Models\Batch;
use App\Models\Institute;
use App\Models\TrainingCenter;
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

        // Dhaka: 23.777176, 90.399452.
        // chittagong : 22.341900, 91.815536.
        // sylhet: 24.886436, 91.880722.
        // Dinajpur : 25.636574, 88.636322.
        // khulna: 22.820000, 89.550003 // 4, 39,
        // Mymensingh: 24.743448, 90.398384. // 8, 27, 294

        $randomNum = $this->faker->numerify('');
        $lat = '23.777176';

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
            'mobile_numbers' => '["' . $this->faker->numerify('017########') . '","' . $this->faker->numerify('017########') . '"]',
            'primary_mobile' => $this->faker->numerify('017########'),
            'email' => $this->faker->email(),
            'config' => $this->faker->sentence,
            'google_map_src' => $this->faker->sentence,
            'logo' => "softbd.jpg"
        ];
    }
    /**
     * Configure the model factory.
     * @return $this
     **/
/*
    public function configure()
    {
        return $this->afterCreating(function (Institute $institute) {
            $trainingCenterId = TrainingCenter::where('institute_id', $institute->id)->random()->id;
            $batch->training_center_id = $trainingCenterId;
            $batch->save();
        });
    }
*/
}
