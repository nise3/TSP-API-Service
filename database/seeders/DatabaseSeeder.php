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
            BranchSeeder::class,
            TrainingCenterSeeder::class,
            CourseSeeder::class,
            BatcheSeeder::class,
        ]);
    }
}
