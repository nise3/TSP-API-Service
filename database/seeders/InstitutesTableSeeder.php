<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InstitutesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();

        DB::table('institutes')->truncate();

        DB::table('institutes')->insert([
            array('id' => '1','institute_type_id' => '1','service_type' => '1','code' => 'SSP00000001','title' => 'যুব উন্নয়ন অধিদপ্তর - গণপ্রজাতন্ত্রী বাংলাদেশ সরকার','title_en' => 'Department of Youth Development - Government of the People\'s Republic of Bangladesh','domain' => 'https://dyd.gov.bd','loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => '23.752199572947212','location_longitude' => '90.42071159519254','google_map_src' => NULL,'address_en' => 'Office of the Deputy Director, 12 Gajanbi Road, Mohammadpur, Dhaka-1206','address' => 'যুব উন্নয়ন অধিদপ্তর,১০৮ মতিঝিল,ঢাকা-১০০০','logo' => 'https://file-phase1.nise.gov.bd/uploads/xxikrApIDeDTpMMpT1EsMoRLpHWo3R1646292100.jfif','country' => 'BD','phone_code' => '880','primary_phone' => '01674248402','phone_numbers' => '[]','primary_mobile' => '01674248402','mobile_numbers' => '[]','email' => 'rahulbgc21@gmail.com','name_of_the_office_head' => 'মো: আজহারুল ইসলাম খান','name_of_the_office_head_en' => 'Md. Azharul Islam Khan','name_of_the_office_head_designation' => 'মহাপরিচালক','name_of_the_office_head_designation_en' => 'DIRECTOR GENERAL','contact_person_name' => 'শেখ মোঃ নাসির উদ্দিন','contact_person_name_en' => 'Sheikh,Md,Nasir uddin','contact_person_mobile' => '01674248402','contact_person_email' => 'rahulbgc21@gmail.com','contact_person_designation' => 'উপপরিচালক(প্রশিক্ষণ)','contact_person_designation_en' => 'Deputy Director(training)','config' => NULL,'row_status' => '1','created_by' => '1','updated_by' => '1','created_at' => '2022-01-10 00:20:48','updated_at' => '2022-03-03 19:21:44','deleted_at' => NULL),
            array('id' => '2','institute_type_id' => '2','service_type' => '1','code' => 'SSP00000002','title' => 'স্ট্রেন্থেনিং ইনক্লুসিভ ডেভেলপমেন্ট ইন চিটাগাং হিল ট্রাক্টস (এসআইডি - সিএইচটি)','title_en' => 'Strengthening Inclusive Development in Chittagong Hill Tracts (SID-CHT)','domain' => 'https://sid-cht.nise.gov.bd','loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => '23.778365382651636','location_longitude' => '90.3794945399175','google_map_src' => 'https://www.google.com/maps/place/IDB+Bhaban,+E%2F8-A,+Dhaka+1207/@23.7781494,90.3773273,17z/data=!3m1!4b1!4m5!3m4!1s0x3755c74e8282b185:0x5e029ded49de5bfc!8m2!3d23.7781494!4d90.379516','address_en' => 'UNDP, UN Offices, 18th Floor, IDB Bhaban,Agargaon, Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh','address' => 'UNDP, UN Offices, 18th Floor, IDB Bhaban,Agargaon, Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh','logo' => 'https://file-phase1.nise.gov.bd/uploads/QmJgm8oWzub35IF0RUrIp6ZTdP59b31645852382.jpeg','country' => 'BD','phone_code' => '880','primary_phone' => '+880 2 55667788','phone_numbers' => '[]','primary_mobile' => '01813628025','mobile_numbers' => '[]','email' => 'marufmazumder.piistech@gmail.com','name_of_the_office_head' => 'Mr. Maruf','name_of_the_office_head_en' => 'Mr. Maruf','name_of_the_office_head_designation' => 'Country Head','name_of_the_office_head_designation_en' => 'Country Head','contact_person_name' => 'Abdur Razzak','contact_person_name_en' => 'Abdur Razzak','contact_person_mobile' => '01813628025','contact_person_email' => 'marufmazumder.piistech@gmail.com','contact_person_designation' => 'Pion','contact_person_designation_en' => 'Pion','config' => NULL,'row_status' => '1','created_by' => '1','updated_by' => '1','created_at' => '2022-01-10 19:06:37','updated_at' => '2022-02-26 17:13:26','deleted_at' => NULL),
            array('id' => '3','institute_type_id' => '1','service_type' => '1','code' => 'SSP00000003','title' => 'বাংলাদেশ শিল্প কারিগরি সহায়তা কেন্দ্র (বিটাক)','title_en' => 'Bangladesh Industrial Technical Assistance Center (BITAC)','domain' => 'https://bitac.gov.bd','loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => '23.760964541805','location_longitude' => '90.402084186509','google_map_src' => 'https://www.google.com/maps?ll=23.760994,90.402052&z=18&t=m&hl=en-US&gl=US&mapclient=embed&cid=7485425113775247879','address_en' => '১১৬ (খ), তেজগাঁও শিল্প এলাকা','address' => '116 (Kha) Tejgaon Industrial Area','logo' => 'https://file.nise3.xyz/uploads/L0dZRaqd8EaSeLla3PYJBwNIQsxewo1642418861.gif','country' => 'BD','phone_code' => '880','primary_phone' => '+88-02-55030046','phone_numbers' => '["+88-02-55030046", "+88-02-55030057"]','primary_mobile' => '01887263793','mobile_numbers' => '["01733341663"]','email' => 'contact@bitac.gov.bd','name_of_the_office_head' => 'জাকিয়া সুলতানা','name_of_the_office_head_en' => 'Zakia Sultana','name_of_the_office_head_designation' => 'মহাপরিচালক','name_of_the_office_head_designation_en' => 'Director General','contact_person_name' => 'আব্দুর রাজ্জাক','contact_person_name_en' => 'Abdur Razzak','contact_person_mobile' => '01887263793','contact_person_email' => 'contact@bitac.gov.bd','contact_person_designation' => 'Pion','contact_person_designation_en' => 'Pion','config' => NULL,'row_status' => '1','created_by' => '1','updated_by' => '1','created_at' => '2022-01-16 21:54:26','updated_at' => '2022-01-31 22:31:52','deleted_at' => NULL),
            array('id' => '6','institute_type_id' => '1','service_type' => '1','code' => 'SSP00000004','title' => 'সমাজসেবা অধিদফতর','title_en' => 'Department of Social Services','domain' => 'https://dss.nise.gov.bd','loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => 'Somajseba Bhabon, Plot No. E-8/B, 1 W Agargaon, Dhaka 1207','address' => 'Somajseba Bhabon, Plot No. E-8/B, 1 W Agargaon, Dhaka 1207','logo' => NULL,'country' => 'BD','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01681111111','mobile_numbers' => '[]','email' => 'dss@gmail.com','name_of_the_office_head' => 'Sheikh Rafiqul Islam','name_of_the_office_head_en' => NULL,'name_of_the_office_head_designation' => 'Director General','name_of_the_office_head_designation_en' => NULL,'contact_person_name' => 'Department of Social Services','contact_person_name_en' => NULL,'contact_person_mobile' => '01681111111','contact_person_email' => 'dss@gmail.com','contact_person_designation' => 'Department of Social Services','contact_person_designation_en' => NULL,'config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-04 01:17:39','updated_at' => '2022-03-05 20:29:53','deleted_at' => NULL),
            array('id' => '8','institute_type_id' => '2','service_type' => '1','code' => 'SSP00000005','title' => 'Strengthening Women’s Ability for Productive New Opportunities','title_en' => 'Strengthening Women’s Ability for Productive New Opportunities','domain' => NULL,'loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => 'Kakrail','address' => 'Kakrail','logo' => NULL,'country' => 'BD','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01730014032','mobile_numbers' => '[]','email' => 'info@swapnobd.org','name_of_the_office_head' => 'HABIBUR RAHMAN','name_of_the_office_head_en' => NULL,'name_of_the_office_head_designation' => 'National Project Director','name_of_the_office_head_designation_en' => NULL,'contact_person_name' => 'swapno','contact_person_name_en' => NULL,'contact_person_mobile' => '01730014032','contact_person_email' => 'info@swapnobd.org','contact_person_designation' => 'swapno','contact_person_designation_en' => NULL,'config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-04 01:26:29','updated_at' => '2022-03-05 20:41:31','deleted_at' => NULL),
            array('id' => '10','institute_type_id' => '2','service_type' => '1','code' => 'SSP00000006','title' => 'National Urban Poverty Reduction Programme','title_en' => 'National Urban Poverty Reduction Programme','domain' => NULL,'loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => '112','location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => 'Dhaka','address' => 'Dhaka','logo' => NULL,'country' => 'BD','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01681111111','mobile_numbers' => '[]','email' => 'nuprp@gmail.com','name_of_the_office_head' => 'Project director','name_of_the_office_head_en' => NULL,'name_of_the_office_head_designation' => 'Project director','name_of_the_office_head_designation_en' => NULL,'contact_person_name' => 'Nuprp','contact_person_name_en' => NULL,'contact_person_mobile' => '01682222222','contact_person_email' => 'email@gmail.com','contact_person_designation' => 'Nuprp','contact_person_designation_en' => NULL,'config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-05 20:19:19','updated_at' => '2022-03-05 20:30:22','deleted_at' => NULL),
            array('id' => '13','institute_type_id' => '2','service_type' => '1','code' => 'SSP00000007','title' => 'Futurenation','title_en' => 'Futurenation','domain' => NULL,'loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => '112','location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => 'Dhaka','address' => 'Dhaka','logo' => NULL,'country' => 'BD','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01681111111','mobile_numbers' => '[]','email' => 'futurenation@gmail.com','name_of_the_office_head' => 'Project director','name_of_the_office_head_en' => NULL,'name_of_the_office_head_designation' => 'Project director','name_of_the_office_head_designation_en' => NULL,'contact_person_name' => 'futurenation','contact_person_name_en' => NULL,'contact_person_mobile' => '01683333333','contact_person_email' => 'futurenation@gmail.com','contact_person_designation' => 'futurenation','contact_person_designation_en' => NULL,'config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-05 20:23:24','updated_at' => '2022-03-05 20:40:37','deleted_at' => NULL),
            array('id' => '18','institute_type_id' => '2','service_type' => '1','code' => 'SSP00000011','title' => 'Women’s Empowerment for Inclusive Growth','title_en' => 'Women’s Empowerment for Inclusive Growth','domain' => NULL,'loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => 'Dhaka','address' => 'Dhaka','logo' => NULL,'country' => 'BD','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01681111111','mobile_numbers' => '[]','email' => 'wing@gmail.com','name_of_the_office_head' => 'Project Director','name_of_the_office_head_en' => NULL,'name_of_the_office_head_designation' => 'Project Director','name_of_the_office_head_designation_en' => NULL,'contact_person_name' => 'Wing','contact_person_name_en' => NULL,'contact_person_mobile' => '01685555555','contact_person_email' => 'wing@gmail.com','contact_person_designation' => 'Wing','contact_person_designation_en' => NULL,'config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-05 20:34:29','updated_at' => '2022-03-05 20:34:29','deleted_at' => NULL),
            array('id' => '20','institute_type_id' => '2','service_type' => '1','code' => 'SSP00000012','title' => 'Institution of Diploma Engineers, Bangladesh (IDEB)','title_en' => 'Institution of Diploma Engineers, Bangladesh (IDEB)','domain' => NULL,'loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => 'IDEB Bhaban, 160/A VIP Rd, Dhaka 1000','address' => 'IDEB Bhaban, 160/A VIP Rd, Dhaka 1000','logo' => NULL,'country' => 'BD','phone_code' => '880','primary_phone' => '02-58314488','phone_numbers' => '[]','primary_mobile' => '01687777777','mobile_numbers' => '[]','email' => 'info@ideb.org.bd','name_of_the_office_head' => 'Ideb Chairman','name_of_the_office_head_en' => NULL,'name_of_the_office_head_designation' => 'Chairman','name_of_the_office_head_designation_en' => NULL,'contact_person_name' => 'IDEB','contact_person_name_en' => NULL,'contact_person_mobile' => '01687777777','contact_person_email' => 'info@ideb.org.bd','contact_person_designation' => 'IDEB','contact_person_designation_en' => NULL,'config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-06 22:57:55','updated_at' => '2022-03-06 22:57:55','deleted_at' => NULL)
        ]);

        Schema::enableForeignKeyConstraints();


    }
}
