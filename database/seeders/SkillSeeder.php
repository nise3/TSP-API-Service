<?php

namespace Database\Seeders;

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
                "title" => "টেকনিকাল রাইটিং",
                "title_en" => "Technical Writing"
            ],
            [
                "title" => "প্রজেক্ট ম্যানেজমেন্ট",
                "title_en" => "Project Management"
            ],
            [
                "title" => "কম্পিউটার প্রোগ্রামিং",
                "title_en" => "Computer Programming"
            ],
            [
                "title" => "ডাটা সাইন্স",
                "title_en" => "Data Science"
            ],
            [
                "title" => "একাউন্টিং",
                "title_en" => "Accounting"
            ],
            [
                "title" => "ইন্টারনেট",
                "title_en" => "Internet"
            ],
            [
                "title" => "ডাটা এন্ট্রি",
                "title_en" => "Data Entry"
            ],
            [
                "title" => "ডাটাবেজ অ্যাডমিনিস্ট্রেশন",
                "title_en" => "Database Administration"
            ],
            [
                "title" => "মাই.এস.কিউ.এল.",
                "title_en" => "MySQL"
            ],
            [
                "title" => "ওরাকল",
                "title_en" => "Oracle"
            ],
            [
                "title" => "উক্স ডিসাইন",
                "title_en" => "UX Design"
            ],
            [
                "title" => "ওয়েব ডিজাইন",
                "title_en" => "Web Design"
            ],
            [
                "title" => "কম্পিউটার প্রযুক্তি",
                "title_en" => "Computer Technology"
            ],
            [
                "title" => "বৈদ্যুতিক প্রযুক্তি",
                "title_en" => "Electrical Technology"
            ],
            [
                "title" => "যান্ত্রিক প্রযুক্তি",
                "title_en" => "Mechanical Technology"
            ],
            [
                "title" => "রাসায়নিক প্রযুক্তি",
                "title_en" => "Chemical Technology"
            ],
            [
                "title" => "ফার্মাসিউটিক্যাল প্রযুক্তি",
                "title_en" => "Pharmaceutical Technology"
            ],
            [
                "title" => "সেলাই",
                "title_en" => "Sewing"
            ],
            [
                "title" => "দর্জি",
                "title_en" => "Tailor"
            ],
            [
                "title" => "ইলেক্ট্রনিক্স মেকানিক",
                "title_en" => "Electronics Mechanic"
            ],
            [
                "title" => "রং মিস্ত্রি",
                "title_en" => "Color Painter"
            ],
            [
                "title" => "ড্রাইভিং",
                "title_en" => "Driving"
            ],
            [
                "title" => "প্লাম্বিং",
                "title_en" => "Plumbing"
            ],
            [
                "title" => "মোটর মেকানিক",
                "title_en" => "Motor Mechanic"
            ],
            [
                "title" => "পোল্ট্রি ফার্মিং",
                "title_en" => "Poultry Farming"
            ],
            [
                "title" => "মাছ চাষ",
                "title_en" => "Fish Farming"
            ],
            [
                "title" => "মেশিন লার্নিং",
                "title_en" => "Machine Learning"
            ],
            [
                "title" => "কম্পিউটার প্রশিক্ষণ",
                "title_en" => "Computer Training"
            ],
            [
                "title" => "সাংগঠনিক সংস্কৃতি",
                "title_en" => "Organizational Culture"
            ],
            [
                "title" => "ব্যবসায়িক লেখা",
                "title_en" => "Business Writing"
            ],
            [
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

        DB::table('skills')->insert($skills);

        Schema::enableForeignKeyConstraints();
    }
}
