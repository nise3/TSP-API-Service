<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
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
        Skill::query()->truncate();

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
                "title" => "কম্পিউটার প্রযুক্তি",
                "title_en" => "Computer Technology"
            ],
            [
                'id' => '14',
                "title" => "বৈদ্যুতিক প্রযুক্তি",
                "title_en" => "Electrical Technology"
            ],
            [
                'id' => '15',
                "title" => "যান্ত্রিক প্রযুক্তি",
                "title_en" => "Mechanical Technology"
            ],
            [
                'id' => '16',
                "title" => "রাসায়নিক প্রযুক্তি",
                "title_en" => "Chemical Technology"
            ],
            [
                'id' => '17',
                "title" => "ফার্মাসিউটিক্যাল প্রযুক্তি",
                "title_en" => "Pharmaceutical Technology"
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
            ],
            [
                'id' => '27',
                "title" => "মেশিন লার্নিং",
                "title_en" => "Machine Learning"
            ],
            [
                'id' => '28',
                "title" => "কম্পিউটার প্রশিক্ষণ",
                "title_en" => "Computer Training"
            ],
            [
                'id' => '29',
                "title" => "সাংগঠনিক সংস্কৃতি",
                "title_en" => "Organizational Culture"
            ],
            [
                'id' => '30',
                "title" => "ব্যবসায়িক লেখা",
                "title_en" => "Business Writing"
            ],
            [
                'id' => '31',
                "title" => "কুকিং",
                "title_en" => "Cooking"
            ],
            [
                "title" => "টেক্সটাইল প্রযুক্তি",
                "title_en" => "Textile Technology"
            ],
            [
                "title" => "টেক্সটাইল ইঞ্জিনিয়ারিং",
                "title_en" => "Textile Engineering"
            ],
            [
                "title" => "কম্পিউটার ইঞ্জিনিয়ারিং",
                "title_en" => "Computer Engineering"
            ],
            [
                "title" => "ফার্মাকোলজি",
                "title_en" => "Pharmacology"
            ],
            [
                "title" => "জৈব রসায়ন",
                "title_en" => "Biochemistry"
            ],
            [
                "title" => "বায়োটেকনোলজি",
                "title_en" => "Biotechnology"
            ],
            [
                "title" => "ফার্মাসিউটিক্যাল ইঞ্জিনিয়ারিং",
                "title_en" => "Pharmaceutical Engineering"
            ],
            [
                "title" => "ফার্মেসি প্রযুক্তি",
                "title_en" => "Pharmacy Technology"
            ],
            [
                "title" => "ভেটেরিনারি প্রযুক্তি",
                "title_en" => "Veterinary Technology"
            ],
            [
                "title" => "পশুচিকিৎসা",
                "title_en" => "Veterinary"
            ],
            [
                "title" => "কৃষিবিদ্যা",
                "title_en" => "Agriculture"
            ],
            [
                "title" => "যন্ত্রপাতি প্রযুক্তি",
                "title_en" => "Appliances Technology"
            ],
            [
                "title" => "যন্ত্রপাতি প্রকৌশল",
                "title_en" => "Appliances Engineering"
            ]
        ];

        Skill::insert($skills);

        Schema::enableForeignKeyConstraints();
    }
}
