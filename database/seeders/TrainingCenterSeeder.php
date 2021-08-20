<?php

namespace Database\Seeders;

use App\Models\TrainingCenter;
use Illuminate\Database\Seeder;

class TrainingCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TrainingCenter::factory()->count(10)->create();
    }
}
