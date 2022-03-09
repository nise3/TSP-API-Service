<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RegisteredTrainingOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('registered_training_organizations')->truncate();
        DB::table('registered_training_organizations')->insert([
            array('id' => '1','institute_id' => '52','code' => 'RTO00000052','title' => 'Bangladesh- German Technical Training Centre (BGTTC)','title_en' => 'Bangladesh- German Technical Training Centre (BGTTC)','loc_division_id' => '3','loc_district_id' => '18','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => NULL,'address' => '1/29 basabo, dhaka','logo' => NULL,'country_id' => '12','phone_code' => '880','primary_phone' => '01918913333','phone_numbers' => '[]','primary_mobile' => '01918913333','mobile_numbers' => '[]','email' => 'testrto@gmail.com','name_of_the_office_head' => 'Shaon','name_of_the_office_head_en' => 'Shaon','name_of_the_office_head_designation' => 'CEO','name_of_the_office_head_designation_en' => 'CEO','contact_person_name' => 'Shaon','contact_person_name_en' => 'Shaon','contact_person_mobile' => '01974332389','contact_person_email' => 'shaon@gmail.com','contact_person_designation' => 'CEO','contact_person_designation_en' => 'CEO','config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-07 17:41:06','updated_at' => '2022-03-07 17:41:06','deleted_at' => NULL),
            array('id' => '2','institute_id' => '52','code' => 'RTO00000053','title' => 'UCEP-Rajshahi Technical School','title_en' => 'UCEP-Rajshahi Technical School','loc_division_id' => '3','loc_district_id' => '23','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => NULL,'address' => '123','logo' => NULL,'country_id' => '2','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01974332387','mobile_numbers' => '[]','email' => 'testrto2@gmail.com','name_of_the_office_head' => 'Shaon','name_of_the_office_head_en' => 'Shaon','name_of_the_office_head_designation' => 'CEO','name_of_the_office_head_designation_en' => 'CEO','contact_person_name' => 'Shaon','contact_person_name_en' => 'Shaon','contact_person_mobile' => '01974332387','contact_person_email' => 'shaon2@gmail.com','contact_person_designation' => 'CEO','contact_person_designation_en' => 'CEO','config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-07 18:43:25','updated_at' => '2022-03-07 18:43:25','deleted_at' => NULL),
            array('id' => '3','institute_id' => '52','code' => 'RTO00000054','title' => 'Institute of Marine Technology, Bagerhat','title_en' => 'Institute of Marine Technology, Bagerhat','loc_division_id' => '5','loc_district_id' => '47','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => NULL,'address' => 'dafads','logo' => NULL,'country_id' => '12','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01819888888','mobile_numbers' => '[]','email' => 'adfasd@gmail.com','name_of_the_office_head' => 'Shaon','name_of_the_office_head_en' => 'Shaon','name_of_the_office_head_designation' => 'CEO','name_of_the_office_head_designation_en' => 'CEO','contact_person_name' => 'Shaon','contact_person_name_en' => 'Shaon','contact_person_mobile' => '01918887777','contact_person_email' => 'adsfadsf@gmail.com','contact_person_designation' => 'CEO','contact_person_designation_en' => 'CEO','config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-08 01:02:12','updated_at' => '2022-03-08 01:02:12','deleted_at' => NULL),
            array('id' => '4','institute_id' => '56','code' => 'RTO00000057','title' => 'Ahsania Mission Syed Sadat ali Memorial Education & Vocational Training Centre, Mohammadpur','title_en' => 'Ahsania Mission Syed Sadat ali Memorial Education & Vocational Training Centre, Mohammadpur','loc_division_id' => '3','loc_district_id' => '20','loc_upazila_id' => NULL,'location_latitude' => NULL,'location_longitude' => NULL,'google_map_src' => NULL,'address_en' => NULL,'address' => '125','logo' => NULL,'country_id' => '12','phone_code' => '880','primary_phone' => NULL,'phone_numbers' => '[]','primary_mobile' => '01918912222','mobile_numbers' => '[]','email' => 'miltondeb2@gmail.com','name_of_the_office_head' => 'Shaon','name_of_the_office_head_en' => 'Shaon','name_of_the_office_head_designation' => 'CEO','name_of_the_office_head_designation_en' => 'CEO','contact_person_name' => 'Shaon','contact_person_name_en' => 'Shaon','contact_person_mobile' => '01974332382','contact_person_email' => 'miltondeb2@gmail.com','contact_person_designation' => 'CEO','contact_person_designation_en' => 'CEO','config' => NULL,'row_status' => '1','created_by' => NULL,'updated_by' => NULL,'created_at' => '2022-03-08 18:41:50','updated_at' => '2022-03-08 18:41:50','deleted_at' => NULL)
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
