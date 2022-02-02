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
//            InstituteSeeder::class,
//            CourseEnrollmentSeeder::class,
        ]);
/*        $this->call(InstitutesTableSeeder::class);
        $this->call(BranchesTableSeeder::class);
        $this->call(TrainingCentersTableSeeder::class);
        $this->call(SspPessimisticLockingsTableSeeder::class);*/
    }
}
