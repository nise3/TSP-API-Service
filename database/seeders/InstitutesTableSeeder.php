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
        
        \DB::table('institutes')->insert(array (
            0 => 
            array (
                'id' => 26,
                'code' => 'sdfsdfsdf',
                'institute_type_id' => 2,
                'title' => 'Softbd',
                'title_en' => NULL,
                'domain' => NULL,
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => 112,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address_en' => NULL,
                'address' => 'Hasan Holdings',
                'logo' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => '01717463510',
                'phone_numbers' => '[]',
                'primary_mobile' => '01812345678',
                'mobile_numbers' => '[]',
                'email' => 'softbd@gmail.com',
                'name_of_the_office_head' => 'Office head',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'test',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'Milton',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01812345679',
                'contact_person_email' => 'softbd@gmail.com',
                'contact_person_designation' => 'Engineer',
                'contact_person_designation_en' => NULL,
                'config' => NULL,
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-09 18:20:48',
                'updated_at' => '2022-01-23 14:37:18',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 27,
                'code' => '234242342',
                'institute_type_id' => 2,
                'title' => 'Softbd 2',
                'title_en' => NULL,
                'domain' => NULL,
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => 112,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address_en' => NULL,
                'address' => 'Hasan holding',
                'logo' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => NULL,
                'phone_numbers' => '[]',
                'primary_mobile' => '01812345670',
                'mobile_numbers' => '[]',
                'email' => 'softbd2@gmail.com',
                'name_of_the_office_head' => 'SoftBD',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'SoftBD',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'Softbd',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01812345670',
                'contact_person_email' => 'softbd2@gmail.com',
                'contact_person_designation' => 'Softbd',
                'contact_person_designation_en' => NULL,
                'config' => NULL,
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-10 13:06:37',
                'updated_at' => '2022-01-10 14:44:00',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 28,
                'code' => '67879678',
                'institute_type_id' => 2,
                'title' => 'লোকাল টেস্ট প্রতিষ্ঠান',
                'title_en' => 'Local test institute',
                'domain' => NULL,
                'loc_division_id' => 6,
                'loc_district_id' => 60,
                'loc_upazila_id' => 481,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address_en' => NULL,
                'address' => 'Dhaka, 1200',
                'logo' => 'https://file.nise3.xyz/uploads/L0dZRaqd8EaSeLla3PYJBwNIQsxewo1642418861.gif',
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => NULL,
                'phone_numbers' => '[]',
                'primary_mobile' => '01887263793',
                'mobile_numbers' => '["01733341663"]',
                'email' => 'rouzex@gmail.com',
                'name_of_the_office_head' => 'Abdur Razzak',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'Abdur Razzak',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'Abdur Razzak',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01887263793',
                'contact_person_email' => 'xdrazzak@gmail.com',
                'contact_person_designation' => 'Abdur Razzak',
                'contact_person_designation_en' => NULL,
                'config' => NULL,
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-16 15:54:26',
                'updated_at' => '2022-01-31 16:31:52',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 31,
                'code' => 'wer23423423',
                'institute_type_id' => 3,
                'title' => 'sss',
                'title_en' => 'ddd',
                'domain' => NULL,
                'loc_division_id' => 2,
                'loc_district_id' => 10,
                'loc_upazila_id' => NULL,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address_en' => NULL,
                'address' => 'Holding: 35/06/23/A, Word: 5, Faridpur Sadar, Faridpur - 7800',
                'logo' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => '01717463510',
                'phone_numbers' => '[]',
                'primary_mobile' => '01717463511',
                'mobile_numbers' => '[]',
                'email' => 'grmunnabd@gmail.com',
                'name_of_the_office_head' => 'MD. EHSANUL HAQUE MUNNA',
                'name_of_the_office_head_en' => 'MD. EHSANUL HAQUE MUNNA',
                'name_of_the_office_head_designation' => 'MD. EHSANUL HAQUE MUNNA',
                'name_of_the_office_head_designation_en' => 'MD. EHSANUL HAQUE MUNNA',
                'contact_person_name' => 'MD. EHSANUL HAQUE MUNNA',
                'contact_person_name_en' => 'MD. EHSANUL HAQUE MUNNA',
                'contact_person_mobile' => '01717463511',
                'contact_person_email' => 'grmunnabd@gmail.com',
                'contact_person_designation' => 'MD. EHSANUL HAQUE MUNNA',
                'contact_person_designation_en' => 'MD. EHSANUL HAQUE MUNNA',
                'config' => NULL,
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-23 14:38:19',
                'updated_at' => '2022-01-23 14:38:54',
                'deleted_at' => '2022-01-23 14:38:54',
            ),
        ));

        Schema::enableForeignKeyConstraints();

        
    }
}