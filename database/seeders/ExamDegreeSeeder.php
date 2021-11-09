<?php

namespace Database\Seeders;

use App\Models\ExamDegree;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ExamDegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        ExamDegree::query()->truncate();
        $examDegrees = [
            [
                "education_level_id" => 1,
                "code" => "PSC",
                "title" => "পিএসসি",
                "title_en" => "PSC"
            ],
            [
                "education_level_id" => 1,
                "code" => "EBTEDAYEE",
                "title" => "ইবতেদায়ি (মাদ্রাসা)",
                "title_en" => "Ebtedayee (Madrasah)"
            ],
            [
                "education_level_id" => 1,
                "code" => "FIVE_PASS",
                "title" => "5 Pass",
                "title_en" => "5 Pass"
            ],
            [
                "education_level_id" => 1,
                "code" => "OTHERS",
                "title" => "Others",
                "title_en" => "Others"
            ],
            [
                "education_level_id" => 2,
                "code" => "JSC",
                "title" => "জেএসসি",
                "title_en" => "JSC"
            ],
            [
                "education_level_id" => 2,
                "code" => "JDC",
                "title" => "জেডিসি (মাদ্রাসা)",
                "title_en" => "JDC (Madrasah)"
            ],
            [
                "education_level_id" => 2,
                "code" => "EIGHT_PASS",
                "title" => "8 Pass",
                "title_en" => "8 Pass"
            ],
            [
                "education_level_id" => 2,
                "code" => "OTHERS",
                "title" => "Others",
                "title_en" => "Others"
            ],
            [
                "education_level_id" => 3,
                "code" => "SSC",
                "title" => "এসএসসি",
                "title_en" => "SSC"
            ],
            [
                "education_level_id" => 3,
                "code" => "O_LEVEL",
                "title" => "ও লেভেল",
                "title_en" => "O Level"
            ],
            [
                "education_level_id" => 3,
                "code" => "DAKHIL",
                "title" => "দাখিল (মাদ্রাসা)",
                "title_en" => "Dakhil (Madrasah)"
            ],
            [
                "education_level_id" => 3,
                "code" => "SSC_VOC",
                "title" => "এসএসসি (ভোকেশনাল)",
                "title_en" => "SSC (Vocational)"
            ],
            [
                "education_level_id" => 3,
                "code" => "OTHERS",
                "title" => "Others",
                "title_en" => "Others"
            ],
            [
                "education_level_id" => 4,
                "code" => "HSC",
                "title" => "এইচএসসি",
                "title_en" => "HSC"
            ],
            [
                "education_level_id" => 4,
                "code" => "A_LEVEL",
                "title" => "এ লেভেল",
                "title_en" => "A Level"
            ],
            [
                "education_level_id" => 4,
                "code" => "ALIM",
                "title" => "আলিম (মাদ্রাসা)",
                "title_en" => "Alim (Madrasah)"
            ],
            [
                "education_level_id" => 4,
                "code" => "HSC_VOC",
                "title" => "এইচএসসি  (ভোকেশনাল)",
                "title_en" => "HSC (Vocational)"
            ],
            [
                "education_level_id" => 4,
                "code" => "OTHERS",
                "title" => "Others",
                "title_en" => "Others"
            ],
            [
                "education_level_id" => 5,
                "code" => "DIPLOMA_IT",
                "title" => "ডিপ্লোমা ইন আইটি",
                "title_en" => "Diploma in IT"
            ],
            [
                "education_level_id" => 6,
                "code" => "BSC",
                "title" => "ব্যাচেলর অফ সায়েন্স",
                "title_en" => "Bachelor of Science"
            ],
            [
                "education_level_id" => 6,
                "code" => "BA",
                "title" => "ব্যাচেলর অফ আর্টস",
                "title_en" => "Bachelor of Arts"
            ],
            [
                "education_level_id" => 7,
                "code" => "MSC",
                "title" => "মাস্টার্স অফ সায়েন্স",
                "title_en" => "Master of Science"
            ]
        ];
        ExamDegree::insert($examDegrees);
        Schema::enableForeignKeyConstraints();
    }
}
