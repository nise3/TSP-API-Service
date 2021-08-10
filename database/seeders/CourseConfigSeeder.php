<?php

namespace Database\Seeders;

use App\Models\CourseConfig;
use Illuminate\Database\Seeder;

class CourseConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourseConfig::factory()->count(10)->create();
    }
}
