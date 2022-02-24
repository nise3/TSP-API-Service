<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Course::query()->truncate();
        $data = array(
            array('id' => '1', 'code' => 'SSP00000002C0000001', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'ব্লক বাটিক', 'title_en' => 'Block batik', 'course_fee' => '500.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file.nise3.xyz/uploads/5KIpbON1IwN3Q8X6FKNjiTSzqBmbjk1644406269.png', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-09 23:32:02', 'updated_at' => '2022-02-16 16:29:45', 'deleted_at' => NULL),
            array('id' => '2', 'code' => 'SSP00000002C0000002', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'ফল চাষ', 'title_en' => 'Fruit cultivation', 'course_fee' => '550.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file.nise3.xyz/uploads/AaiquRqU1qD4LvBQIxyscGN5U5H3gu1644406594.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[true,false],\\"freedom_fighter_info\\":[true,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[true,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-09 23:36:38', 'updated_at' => '2022-02-16 17:16:19', 'deleted_at' => NULL),
            array('id' => '3', 'code' => 'SSP00000002C0000003', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '2', 'language_medium' => '2', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'দর্জি শিল্প', 'title_en' => 'Tailoring industry', 'course_fee' => '700.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file.nise3.xyz/uploads/8LLbDJT6j8vrtNBWHmWd41gEouASTu1644406751.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-09 23:39:15', 'updated_at' => '2022-02-16 16:46:42', 'deleted_at' => NULL),
            array('id' => '4', 'code' => 'SSP00000001C0000001', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'গবাদিপশু, হাঁস-মুরগী পালন, প্রাথমিক চিকিৎসা, মৎস্য চাষ ও কৃষি” বিষয়ক প্রশিক্ষণ', 'title_en' => 'Training on Livestock, Poultry, First Aid, Fisheries and Agriculture', 'course_fee' => '50.00', 'duration' => '60.00', 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/SntUTQfmsLuzSRYxQUTXpeOOQ6XuI11644487457.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[true,false],\\"freedom_fighter_info\\":[true,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[true,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-10 21:17:15', 'updated_at' => '2022-02-16 17:20:50', 'deleted_at' => NULL),
            array('id' => '5', 'code' => 'SSP00000001C0000002', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '2', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'পোষাক তৈরী বিষয়ক প্রশিক্ষণ', 'title_en' => 'Garment making training', 'course_fee' => '500.00', 'duration' => '30.00', 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/7wbGqvCK2usbWkCBqYKUXnVjlvD03j1644484750.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-10 21:19:22', 'updated_at' => '2022-02-16 16:48:03', 'deleted_at' => NULL),
            array('id' => '6', 'code' => 'SSP00000001C0000003', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'ব্লক বাটিক ও স্ক্রীন প্রিন্টিং প্রশিক্ষণ', 'title_en' => 'Block batik and screen printing training', 'course_fee' => '50.00', 'duration' => '10.00', 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/0nbsfp07w1FAgzxuZm76ffOzydmAae1644485015.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-10 21:23:43', 'updated_at' => '2022-02-16 16:48:29', 'deleted_at' => NULL),
            array('id' => '7', 'code' => 'SSP00000001C0000004', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'কম্পিউটার বেসিক এন্ড আইসিটি এ্যাপ্লিকেশন', 'title_en' => 'Computer Basic and ICT Applications', 'course_fee' => '1000.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/JTSSmSOQKl5eWGxWWz50eATIXyraGw1644487223.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-10 22:00:27', 'updated_at' => '2022-02-16 16:48:52', 'deleted_at' => NULL),
            array('id' => '8', 'code' => 'SSP00000001C0000005', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'ইলেকট্রনিক্স প্রশিক্ষণ', 'title_en' => 'Electronics training', 'course_fee' => '400.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/w6wZjV6aGRqc3GY5Ha8IIx1KazW7ze1644487311.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-10 22:01:55', 'updated_at' => '2022-02-16 16:49:13', 'deleted_at' => NULL),
            array('id' => '9', 'code' => 'SSP00000001C0000006', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'পোশাক তৈরী', 'title_en' => 'Clothing', 'course_fee' => '0.00', 'duration' => '25.00', 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/DYI3gLIMwVVi4QmNXD5Zd20ZOBieTJ1644487367.png', 'application_form_settings' => '"{\\"ethnic_group_info\\":[true,false],\\"freedom_fighter_info\\":[true,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[true,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-10 22:02:50', 'updated_at' => '2022-02-16 17:14:26', 'deleted_at' => NULL),
            array('id' => '10', 'code' => 'SSP00000002C0000004', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '2', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'টেকনিক্যাল ইন্টারভিউ', 'title_en' => 'Technical Interview', 'course_fee' => '0.00', 'duration' => '60.00', 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/1jzhqOYIP4Zv3z8z9jMHTfLrrdZllr1644813808.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[true,false],\\"psc_passing_info\\":[false,false],\\"jsc_passing_info\\":[true,false],\\"ssc_passing_info\\":[false,false],\\"hsc_passing_info\\":[true,false],\\"diploma_passing_info\\":[false,false],\\"honors_passing_info\\":[false,false],\\"masters_passing_info\\":[false,false],\\"phd_passing_info\\":[false,false],\\"occupation_info\\":[true,false],\\"guardian_info\\":[true,false],\\"miscellaneous_info\\":[true,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-14 16:43:34', 'updated_at' => '2022-02-16 00:22:20', 'deleted_at' => NULL),
            array('id' => '11', 'code' => 'SSP00000002C0000005', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '2', 'language_medium' => '2', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'রিএ্যাক্ট', 'title_en' => 'React', 'course_fee' => '12000.00', 'duration' => '40.00', 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/mTCL341DGqWmEFRBRWEjLyQzwc562O1644815204.png', 'application_form_settings' => '"{\\"ethnic_group_info\\":[true,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[true,false],\\"psc_passing_info\\":[false,false],\\"jsc_passing_info\\":[true,false],\\"ssc_passing_info\\":[false,false],\\"hsc_passing_info\\":[true,false],\\"diploma_passing_info\\":[false,false],\\"honors_passing_info\\":[true,false],\\"masters_passing_info\\":[false,false],\\"phd_passing_info\\":[false,false],\\"occupation_info\\":[true,false],\\"guardian_info\\":[true,false],\\"miscellaneous_info\\":[true,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-14 17:06:49', 'updated_at' => '2022-02-16 00:23:10', 'deleted_at' => NULL),
            array('id' => '12', 'code' => 'SSP00000001C0000007', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '2', 'language_medium' => '2', 'branch_id' => NULL, 'program_id' => '9', 'title' => 'মাইএসকিউয়েল', 'title_en' => 'mysql', 'course_fee' => '0.00', 'duration' => '3.00', 'overview' => 'well', 'overview_en' => 'well', 'target_group' => 'all', 'target_group_en' => 'all', 'objectives' => 'good', 'objectives_en' => 'good', 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => 'hard', 'training_methodology_en' => 'hard', 'evaluation_system' => 'complex', 'evaluation_system_en' => 'complex', 'prerequisite' => 'rdbms', 'prerequisite_en' => 'rdbms', 'eligibility' => 'few', 'eligibility_en' => 'few', 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/0NgcRGxGvSNTwGElrVqYgrKtntuZ5H1644834639.png', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[true,false],\\"education_info\\":[true,false],\\"occupation_info\\":[true,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[true,false],\\"psc_passing_info\\":[false,false],\\"jsc_passing_info\\":[false,false],\\"ssc_passing_info\\":[true,false],\\"hsc_passing_info\\":[true,false],\\"diploma_passing_info\\":[false,false],\\"honors_passing_info\\":[true,false],\\"masters_passing_info\\":[false,false],\\"phd_passing_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-14 22:31:03', 'updated_at' => '2022-02-16 17:11:22', 'deleted_at' => NULL),
            array('id' => '13', 'code' => 'SSP00000001C0000008', 'institute_id' => '1', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'পরীক্ষার কোর্স সম্পাদিত', 'title_en' => NULL, 'course_fee' => '34.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => NULL, 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-15 15:40:23', 'updated_at' => '2022-02-15 15:51:02', 'deleted_at' => '2022-02-15 15:51:02'),
            array('id' => '14', 'code' => 'SSP00000002C0000006', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '1', 'language_medium' => '2', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'কম্পিউটার প্রশিক্ষণ', 'title_en' => 'Computer Training', 'course_fee' => '10000.00', 'duration' => NULL, 'overview' => NULL, 'overview_en' => NULL, 'target_group' => NULL, 'target_group_en' => NULL, 'objectives' => NULL, 'objectives_en' => NULL, 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => NULL, 'training_methodology_en' => NULL, 'evaluation_system' => NULL, 'evaluation_system_en' => NULL, 'prerequisite' => NULL, 'prerequisite_en' => NULL, 'eligibility' => NULL, 'eligibility_en' => NULL, 'cover_image' => NULL, 'application_form_settings' => '"{\\"ethnic_group_info\\":[true,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[true,false],\\"psc_passing_info\\":[false,false],\\"jsc_passing_info\\":[true,false],\\"ssc_passing_info\\":[false,false],\\"hsc_passing_info\\":[true,false],\\"diploma_passing_info\\":[false,false],\\"honors_passing_info\\":[true,false],\\"masters_passing_info\\":[false,false],\\"phd_passing_info\\":[false,false],\\"occupation_info\\":[true,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[true,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-15 23:19:06', 'updated_at' => '2022-02-16 00:25:50', 'deleted_at' => NULL),
            array('id' => '15', 'code' => 'SSP0000000200000007', 'institute_id' => '2', 'industry_association_id' => NULL, 'level' => '2', 'language_medium' => '1', 'branch_id' => NULL, 'program_id' => NULL, 'title' => 'নোড জেএস এবং প্রতিক্রিয়া সহ মাইক্রোসার্ভিস', 'title_en' => 'Microservices with Node JS and React', 'course_fee' => '1000.00', 'duration' => '3.00', 'overview' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'overview_en' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'target_group' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'target_group_en' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'objectives' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'objectives_en' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'lessons' => NULL, 'lessons_en' => NULL, 'training_methodology' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'training_methodology_en' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'evaluation_system' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'evaluation_system_en' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'prerequisite' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'prerequisite_en' => 'Basic knowledge of Javascript and Express is required
Knowledge of React is good, but not needed
You must be familiar and comfortable with the command line', 'eligibility' => 'Javascript engineers looking to build large, scalable applications
This course is *not* designed for sysadmins focused on infrastructure deployment', 'eligibility_en' => 'Javascript engineers looking to build large, scalable applications
This course is *not* designed for sysadmins focused on infrastructure deployment', 'cover_image' => 'https://file-phase1.nise.gov.bd/uploads/Jg8n7FkUrdPxUKhecbBXTNzCzcG8wD1644994445.jpg', 'application_form_settings' => '"{\\"ethnic_group_info\\":[false,false],\\"freedom_fighter_info\\":[false,false],\\"disability_info\\":[false,false],\\"education_info\\":[false,false],\\"occupation_info\\":[false,false],\\"guardian_info\\":[false,false],\\"miscellaneous_info\\":[false,false]}"', 'row_status' => '1', 'created_by' => NULL, 'updated_by' => NULL, 'created_at' => '2022-02-16 18:56:07', 'updated_at' => '2022-02-16 18:56:07', 'deleted_at' => NULL)

        );

        Course::insert($data);
        Schema::disableForeignKeyConstraints();
    }

}
