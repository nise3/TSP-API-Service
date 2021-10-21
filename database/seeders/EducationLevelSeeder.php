<?php

namespace Database\Seeders;

use App\Models\BaseModel;
use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $educationLevels = [
            [
                "id" => 1,
                "code" => EducationLevel::EDUCATION_LEVEL_PSC_5_PASS,
                "title" => "PSC/5 Pass",
                "title_en" => "PSC/5 Pass",
            ],
            [
                "id" => 2,
                "code" => EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS,
                "title" => "JSC/JDC/8 Pass",
                "title_en" => "JSC/JDC/8 Pass",
            ],
            [
                "id" => 3,
                "code" => EducationLevel::EDUCATION_LEVEL_SECONDARY,
                "title" => "Secondary",
                "title_en" => "Secondary",
            ],
            [
                "id" => 4,
                "code" => EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY,
                "title" => "Higher Secondary",
                "title_en" => "Higher Secondary",
            ],
            [
                "id" => 5,
                "code" => EducationLevel::EDUCATION_LEVEL_DIPLOMA,
                "title" => "Diploma",
                "title_en" => "Diploma",
            ],
            [
                "id" => 6,
                "code" => EducationLevel::EDUCATION_LEVEL_BACHELOR,
                "title" => "Bachelor/Honors",
                "title_en" => "Bachelor/Honors",
            ],
            [
                "id" => 7,
                "code" => EducationLevel::EDUCATION_LEVEL_MASTERS,
                "title" => "Masters",
                "title_en" => "Masters",
            ],
            [
                "id" => 8,
                "code" => EducationLevel::EDUCATION_LEVEL_PHD,
                "title" => "PhD (Doctor of Philosophy)",
                "title_en" => "PhD (Doctor of Philosophy)",
            ]
        ];
        EducationLevel::insert($educationLevels);
    }
}
