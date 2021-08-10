<?php

namespace Database\Seeders;

use App\Models\Programme;
use Illuminate\Database\Seeder;

class ProgrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Programme::factory()->count(10)->create();
    }
}
