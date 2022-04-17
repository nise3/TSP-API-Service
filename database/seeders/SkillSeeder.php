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
        DB::table('skills')->insert([
            array('id' => '1', 'title' => 'টেকনিকাল রাইটিং', 'title_en' => 'Technical Writing', 'deleted_at' => NULL),
            array('id' => '2', 'title' => 'প্রজেক্ট ম্যানেজমেন্ট', 'title_en' => 'Project Management', 'deleted_at' => NULL),
            array('id' => '3', 'title' => 'কম্পিউটার প্রোগ্রামিং', 'title_en' => 'Computer Programming', 'deleted_at' => NULL),
            array('id' => '4', 'title' => 'ডাটা সাইন্স', 'title_en' => 'Data Science', 'deleted_at' => NULL),
            array('id' => '5', 'title' => 'একাউন্টিং', 'title_en' => 'Accounting', 'deleted_at' => NULL),
            array('id' => '6', 'title' => 'ইন্টারনেট', 'title_en' => 'Internet', 'deleted_at' => NULL),
            array('id' => '7', 'title' => 'ডাটা এন্ট্রি', 'title_en' => 'Data Entry', 'deleted_at' => NULL),
            array('id' => '8', 'title' => 'ডাটাবেজ অ্যাডমিনিস্ট্রেশন', 'title_en' => 'Database Administration', 'deleted_at' => NULL),
            array('id' => '9', 'title' => 'মাই.এস.কিউ.এল.', 'title_en' => 'MySQL', 'deleted_at' => NULL),
            array('id' => '10', 'title' => 'ওরাকল', 'title_en' => 'Oracle', 'deleted_at' => NULL),
            array('id' => '11', 'title' => 'উক্স ডিসাইন', 'title_en' => 'UX Design', 'deleted_at' => NULL),
            array('id' => '12', 'title' => 'ওয়েব ডিজাইন', 'title_en' => 'Web Design', 'deleted_at' => NULL),
            array('id' => '13', 'title' => 'কম্পিউটার প্রযুক্তি', 'title_en' => 'Computer Technology', 'deleted_at' => NULL),
            array('id' => '14', 'title' => 'বৈদ্যুতিক প্রযুক্তি', 'title_en' => 'Electrical Technology', 'deleted_at' => NULL),
            array('id' => '15', 'title' => 'যান্ত্রিক প্রযুক্তি', 'title_en' => 'Mechanical Technology', 'deleted_at' => NULL),
            array('id' => '16', 'title' => 'রাসায়নিক প্রযুক্তি', 'title_en' => 'Chemical Technology', 'deleted_at' => NULL),
            array('id' => '17', 'title' => 'ফার্মাসিউটিক্যাল প্রযুক্তি', 'title_en' => 'Pharmaceutical Technology', 'deleted_at' => NULL),
            array('id' => '18', 'title' => 'সেলাই', 'title_en' => 'Sewing', 'deleted_at' => NULL),
            array('id' => '19', 'title' => 'দর্জি', 'title_en' => 'Tailor', 'deleted_at' => NULL),
            array('id' => '20', 'title' => 'ইলেক্ট্রনিক্স মেকানিক', 'title_en' => 'Electronics Mechanic', 'deleted_at' => NULL),
            array('id' => '21', 'title' => 'রং মিস্ত্রি', 'title_en' => 'Color Painter', 'deleted_at' => NULL),
            array('id' => '22', 'title' => 'ড্রাইভিং', 'title_en' => 'Driving', 'deleted_at' => NULL),
            array('id' => '23', 'title' => 'প্লাম্বিং', 'title_en' => 'Plumbing', 'deleted_at' => NULL),
            array('id' => '24', 'title' => 'মোটর মেকানিক', 'title_en' => 'Motor Mechanic', 'deleted_at' => NULL),
            array('id' => '25', 'title' => 'পোল্ট্রি ফার্মিং', 'title_en' => 'Poultry Farming', 'deleted_at' => NULL),
            array('id' => '26', 'title' => 'মাছ চাষ', 'title_en' => 'Fish Farming', 'deleted_at' => NULL),
            array('id' => '27', 'title' => 'মেশিন লার্নিং', 'title_en' => 'Machine Learning', 'deleted_at' => NULL),
            array('id' => '28', 'title' => 'কম্পিউটার প্রশিক্ষণ', 'title_en' => 'Computer Training', 'deleted_at' => NULL),
            array('id' => '29', 'title' => 'সাংগঠনিক সংস্কৃতি', 'title_en' => 'Organizational Culture', 'deleted_at' => NULL),
            array('id' => '30', 'title' => 'ব্যবসায়িক লেখা', 'title_en' => 'Business Writing', 'deleted_at' => NULL),
            array('id' => '31', 'title' => 'কুকিং', 'title_en' => 'Cooking', 'deleted_at' => NULL),
            array('id' => '32', 'title' => 'টেক্সটাইল প্রযুক্তি', 'title_en' => 'Textile Technology', 'deleted_at' => NULL),
            array('id' => '33', 'title' => 'টেক্সটাইল ইঞ্জিনিয়ারিং', 'title_en' => 'Textile Engineering', 'deleted_at' => NULL),
            array('id' => '34', 'title' => 'কম্পিউটার ইঞ্জিনিয়ারিং', 'title_en' => 'Computer Engineering', 'deleted_at' => NULL),
            array('id' => '35', 'title' => 'ফার্মাকোলজি', 'title_en' => 'Pharmacology', 'deleted_at' => NULL),
            array('id' => '36', 'title' => 'জৈব রসায়ন', 'title_en' => 'Biochemistry', 'deleted_at' => NULL),
            array('id' => '37', 'title' => 'বায়োটেকনোলজি', 'title_en' => 'Biotechnology', 'deleted_at' => NULL),
            array('id' => '38', 'title' => 'ফার্মাসিউটিক্যাল ইঞ্জিনিয়ারিং', 'title_en' => 'Pharmaceutical Engineering', 'deleted_at' => NULL),
            array('id' => '39', 'title' => 'ফার্মেসি প্রযুক্তি', 'title_en' => 'Pharmacy Technology', 'deleted_at' => NULL),
            array('id' => '40', 'title' => 'ভেটেরিনারি প্রযুক্তি', 'title_en' => 'Veterinary Technology', 'deleted_at' => NULL),
            array('id' => '41', 'title' => 'পশুচিকিৎসা', 'title_en' => 'Veterinary', 'deleted_at' => NULL),
            array('id' => '42', 'title' => 'কৃষিবিদ্যা', 'title_en' => 'Agriculture', 'deleted_at' => NULL),
            array('id' => '43', 'title' => 'যন্ত্রপাতি প্রযুক্তি', 'title_en' => 'Appliances Technology', 'deleted_at' => NULL),
            array('id' => '44', 'title' => 'যন্ত্রপাতি প্রকৌশল', 'title_en' => 'Appliances Engineering', 'deleted_at' => NULL)
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
