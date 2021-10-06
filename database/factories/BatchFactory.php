<?php

namespace Database\Factories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        return [
            'number_of_seats' => $this->faker->numberBetween(30, 100),
            'registration_start_date' => $this->faker->date('2021-7-21'),
            'registration_end_date' => $this->faker->date('2021-8-11'),
            'batch_start_date' => $this->faker->date('2021-8-21'),
            'batch_end_date' => $this->faker->date('2021-11-21')
        ];
    }
}
