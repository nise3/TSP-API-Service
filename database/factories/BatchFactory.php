<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Programme;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $trainingCenter = TrainingCenter::inRandomOrder()->first();
    	return [
            'training_center_id' => $trainingCenter->id
    	];
    }
}
