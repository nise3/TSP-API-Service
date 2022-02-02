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

        DB::table('institutes')->insert(array (
            array (
                'id' => 1,
                'code' => 'SSP00000001',
                'institute_type_id' => 1,
                'title' => 'যুব উন্নয়ন অধিদপ্তর - গণপ্রজাতন্ত্রী বাংলাদেশ সরকার',
                'title_en' => 'Department of Youth Development - Government of the People\'s Republic of Bangladesh',
                'domain' => 'https://dyd.gov.bd',
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => NULL,
                'location_latitude' => 23.752199572947212,
                'location_longitude' => 90.42071159519254,
                'google_map_src' => NULL,
                'address_en' => 'Office of the Deputy Director, 12 Gajanbi Road, Mohammadpur, Dhaka-1206',
                'address' => 'Office of the Deputy Director, 12 Gajanbi Road, Mohammadpur, Dhaka-1206',
                'logo' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => '01717463510',
                'phone_numbers' => '[]',
                'primary_mobile' => '01812345678',
                'mobile_numbers' => '[]',
                'email' => 'contact@dyd.gov.bd',
                'name_of_the_office_head' => 'মো: আজহারুল ইসলাম খান',
                'name_of_the_office_head_en' => 'Md. Azharul Islam Khan',
                'name_of_the_office_head_designation' => 'মহাপরিচালক',
                'name_of_the_office_head_designation_en' => 'DIRECTOR GENERAL',
                'contact_person_name' => 'Milton',
                'contact_person_name_en' => 'Milton',
                'contact_person_mobile' => '01812345679',
                'contact_person_email' => 'contact@dyd.gov.bd',
                'contact_person_designation' => 'Engineer',
                'contact_person_designation_en' => 'Engineer',
                'config' => NULL,
                'row_status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2022-01-09 18:20:48',
                'updated_at' => '2022-01-23 14:37:18',
                'deleted_at' => NULL,
            ),
            array (
                'id' => 2,
                'code' => 'SSP00000002',
                'institute_type_id' => 2,
                'title' => 'স্ট্রেন্থেনিং ইনক্লুসিভ ডেভেলপমেন্ট ইন চিটাগাং হিল ট্রাক্টস (এসআইডি - সিএইচটি)',
                'title_en' => 'Strengthening Inclusive Development in Chittagong Hill Tracts (SID-CHT)',
                'domain' => 'sid-cht.nise.gov.bd',
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => NULL,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address_en' => 'UNDP, UN Offices, 18th Floor, IDB Bhaban,Agargaon, Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh',
                'address' => 'UNDP, UN Offices, 18th Floor, IDB Bhaban,Agargaon, Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh',
                'logo' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => '+880 2 55667788',
                'phone_numbers' => '[]',
                'primary_mobile' => '01812345670',
                'mobile_numbers' => '[]',
                'email' => 'registry.bd@undp.org',
                'name_of_the_office_head' => 'SoftBD',
                'name_of_the_office_head_en' => NULL,
                'name_of_the_office_head_designation' => 'SoftBD',
                'name_of_the_office_head_designation_en' => NULL,
                'contact_person_name' => 'Softbd',
                'contact_person_name_en' => NULL,
                'contact_person_mobile' => '01812345670',
                'contact_person_email' => 'registry.bd@undp.org',
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
                'code' => 'SSP00000003',
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
            )
        ));

        Schema::enableForeignKeyConstraints();


    }
}
