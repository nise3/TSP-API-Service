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
            GeoLocationDatabaseSeeder::class,
            SkillSeeder::class,
            PhysicalDisabilitySeeder::class,
            EducationLevelSeeder::class,
            EduGroupSeeder::class,
            EduBoardSeeder::class,
            ExamDegreeSeeder::class,
            CountryTableSeeder::class,
            SspPessimisticLockingsTableSeeder::class,
//            CoursesTableSeeder::class,
//            BatchesTableSeeder::class,
//            TrainingCentersTableSeeder::class,
//            InstitutesTableSeeder::class,

        ]);
    }
}
