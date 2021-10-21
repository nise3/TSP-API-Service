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
            InstituteSeeder::class,
            CourseBatchCompositeSeeder::class,
            CourseEnrollmentSeeder::class,
            EduGroupSeeder::class,
            EduBoardSeeder::class,
            ExamDegreeSeeder::class,
            EducationLevelSeeder::class,
            PhysicalDisabilitySeeder::class
        ]);
    }
}
