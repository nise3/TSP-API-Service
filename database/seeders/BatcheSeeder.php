<?php

namespace Database\Seeders;

use App\Models\Batche;
use Illuminate\Database\Seeder;

class BatcheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Batche::factory()->count(10)->create();
    }
}
