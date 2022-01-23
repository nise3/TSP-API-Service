<?php

namespace Database\Factories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    /**
     * @throws \Exception
     */
    public function definition(): array
    {
        /** @var \DateTime $registrationStartDate */
        $title = $this->faker->name();
        $titleEn = $this->faker->name();
        $registrationStartDate = $this->faker->dateTimeBetween('-8 months', '-1 months');
        $registrationStartDate = Carbon::parse($registrationStartDate); //createFromDate($registrationStartDate);
        $intRand = random_int(7, 30);
        $registrationEndDate = $registrationStartDate->addDays($intRand);
        $intRand = random_int(7, 14);
        $batchStartDate = $registrationEndDate->addDays($intRand);
        $batchEndDate = $batchStartDate->addDays(60);
        return [
            'title' => $title,
            'title_en' => $titleEn,
            'number_of_seats' => $this->faker->numberBetween(30, 100),
            'registration_start_date' => $registrationStartDate,
            'registration_end_date' => $registrationEndDate,
            'batch_start_date' => $batchStartDate,
            'batch_end_date' => $batchEndDate
        ];
    }


}
