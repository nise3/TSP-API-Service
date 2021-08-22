<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Programme;
use App\Models\TrainingCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $branch = Branch::inRandomOrder()->first();
        $course = Course::inRandomOrder()->first();
        $trainingCenter = TrainingCenter::inRandomOrder()->first();
        $programme = Programme::inRandomOrder()->first();
    	return [
            'course_id' => $course->id,
            'training_center_id' => $trainingCenter->id,
            'programme_id' => $programme->id,
            'branch_id' => $branch->id,
    	];
    }
}
