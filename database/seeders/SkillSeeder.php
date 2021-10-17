<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('skills')->truncate();
        $skills = [
            [
                'id' => '1',
                "title" => "টেকনিকাল রাইটিং",
                "title_en" => "Technical Writing"
            ],
            [
                'id' => '2',
                "title" => "প্রজেক্ট ম্যানেজমেন্ট",
                "title_en" => "Project Management"
            ],
            [
                'id' => '3',
                "title" => "কম্পিউটার প্রোগ্রামিং",
                "title_en" => "Computer Programming"
            ],
            [
                'id' => '4',
                "title" => "ডাটা সাইন্স",
                "title_en" => "Data Science"
            ],
            [
                'id' => '5',
                "title" => "একাউন্টিং",
                "title_en" => "Accounting"
            ],
            [
                'id' => '6',
                "title" => "ইন্টারনেট",
                "title_en" => "Internet"
            ],
            [
                'id' => '7',
                "title" => "ডাটা এন্ট্রি",
                "title_en" => "Data Entry"
            ],
            [
                'id' => '8',
                "title" => "ডাটাবেজ অ্যাডমিনিস্ট্রেশন",
                "title_en" => "Database Administration"
            ],
            [
                'id' => '9',
                "title" => "মাই.এস.কিউ.এল.",
                "title_en" => "MySQL"
            ],
            [
                'id' => '10',
                "title" => "ওরাকল",
                "title_en" => "Oracle"
            ],
            [
                'id' => '11',
                "title" => "উক্স ডিসাইন",
                "title_en" => "UX Design"
            ],
            [
                'id' => '12',
                "title" => "ওয়েব ডিজাইন",
                "title_en" => "Web Design"
            ],
            [
                'id' => '13',
                "title" => "কম্পিউটার মেকানিক",
                "title_en" => "Computer Mechanic"
            ],
            [
                'id' => '14',
                "title" => "বৈদ্যুতিক মেকানিক",
                "title_en" => "Electrical Mechanic"
            ],
            [
                'id' => '15',
                "title" => "যান্ত্রিক মেকানিক",
                "title_en" => "Mechanical Mechanic"
            ],
            [
                'id' => '16',
                "title" => "রাসায়নিক কারিগর",
                "title_en" => "Chemical Technician"
            ],
            [
                'id' => '17',
                "title" => "ফার্মাসিউটিক্যাল কারিগর",
                "title_en" => "Pharmaceutical Technician"
            ],
            [
                'id' => '18',
                "title" => "সেলাই",
                "title_en" => "Sewing"
            ],
            [
                'id' => '19',
                "title" => "দর্জি",
                "title_en" => "Tailor"
            ],
            [
                'id' => '20',
                "title" => "ইলেক্ট্রনিক্স মেকানিক",
                "title_en" => "Electronics Mechanic"
            ],
            [
                'id' => '21',
                "title" => "রং মিস্ত্রি",
                "title_en" => "Color Painter"
            ],
            [
                'id' => '22',
                "title" => "ড্রাইভিং",
                "title_en" => "Driving"
            ],
            [
                'id' => '23',
                "title" => "প্লাম্বিং",
                "title_en" => "Plumbing"
            ],
            [
                'id' => '24',
                "title" => "মোটর মেকানিক",
                "title_en" => "Motor Mechanic"
            ],
            [
                'id' => '25',
                "title" => "পোল্ট্রি ফার্মিং",
                "title_en" => "Poultry Farming"
            ],
            [
                'id' => '26',
                "title" => "মাছ চাষ",
                "title_en" => "Fish Farming"
            ]
        ];

        Skill::insert($skills);

        Schema::enableForeignKeyConstraints();
    }
}
