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
            array('id' => '1', 'education_level_id' => '1', 'code' => 'PSC', 'title_en' => 'PSC', 'title' => 'পিএসসি', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '2', 'education_level_id' => '1', 'code' => 'EBTEDAYEE', 'title_en' => 'Ebtedayee (Madrasah)', 'title' => 'ইবতেদায়ি (মাদ্রাসা)', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '3', 'education_level_id' => '1', 'code' => 'FIVE_PASS', 'title_en' => '5 Pass', 'title' => '5 Pass', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '4', 'education_level_id' => '1', 'code' => 'OTHERS', 'title_en' => 'Others', 'title' => 'Others', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '5', 'education_level_id' => '2', 'code' => 'JSC', 'title_en' => 'JSC', 'title' => 'জেএসসি', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '6', 'education_level_id' => '2', 'code' => 'JDC', 'title_en' => 'JDC (Madrasah)', 'title' => 'জেডিসি (মাদ্রাসা)', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '7', 'education_level_id' => '2', 'code' => 'EIGHT_PASS', 'title_en' => '8 Pass', 'title' => '8 Pass', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '8', 'education_level_id' => '2', 'code' => 'OTHERS', 'title_en' => 'Others', 'title' => 'Others', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '9', 'education_level_id' => '3', 'code' => 'SSC', 'title_en' => 'JSC', 'title' => 'এসএসসি', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '10', 'education_level_id' => '3', 'code' => 'O_LEVEL', 'title_en' => 'O Level', 'title' => 'ও লেভেল', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '11', 'education_level_id' => '3', 'code' => 'DAKHIL', 'title_en' => 'Dakhil (Madrasah)', 'title' => 'দাখিল (মাদ্রাসা)', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '12', 'education_level_id' => '3', 'code' => 'SSC_VOC', 'title_en' => 'SSC (Vocational)', 'title' => 'এসএসসি (ভোকেশনাল)', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '13', 'education_level_id' => '3', 'code' => 'OTHERS', 'title_en' => 'Others', 'title' => 'Others', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '14', 'education_level_id' => '4', 'code' => 'HSC', 'title_en' => 'HSC', 'title' => 'এইচএসসি', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '15', 'education_level_id' => '4', 'code' => 'A_LEVEL', 'title_en' => 'A Level', 'title' => 'এ লেভেল', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '16', 'education_level_id' => '4', 'code' => 'ALIM', 'title_en' => 'Alim (Madrasah)', 'title' => 'আলিম (মাদ্রাসা)', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '17', 'education_level_id' => '4', 'code' => 'HSC_VOC', 'title_en' => 'HSC (Vocational)', 'title' => 'এইচএসসি  (ভোকেশনাল)', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '18', 'education_level_id' => '4', 'code' => 'OTHERS', 'title_en' => 'Others', 'title' => 'Others', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '19', 'education_level_id' => '5', 'code' => 'DIPLOMA_IT', 'title_en' => 'Diploma in IT', 'title' => 'ডিপ্লোমা ইন আইটি', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '20', 'education_level_id' => '6', 'code' => 'BSC', 'title_en' => 'Bachelor of Science', 'title' => 'ব্যাচেলর অফ সায়েন্স', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '21', 'education_level_id' => '6', 'code' => 'BA', 'title_en' => 'Bachelor of Arts', 'title' => 'ব্যাচেলর অফ আর্টস', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL),
            array('id' => '22', 'education_level_id' => '7', 'code' => 'MSC', 'title_en' => 'Master of Science', 'title' => 'মাস্টার্স অফ সায়েন্স', 'row_status' => '1', 'created_at' => NULL, 'updated_at' => NULL, 'deleted_at' => NULL)
        ];
        ExamDegree::insert($examDegrees);
        Schema::enableForeignKeyConstraints();
    }
}
