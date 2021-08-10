<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            InstituteSeeder::class,
            ProgrammeSeeder::class,
            TrainingCenterSeeder::class,
            CourseSeeder::class,
            BranchSeeder::class,
            CourseConfigSeeder::class,
        ]);
    }
}
