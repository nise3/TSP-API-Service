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

        DB::table('institutes')->insert(array(
            array(
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
                'primary_phone' => '01674248402',
                'phone_numbers' => '[]',
                'primary_mobile' => '01674248402',
                'mobile_numbers' => '[]',
                'email' => 'rahulbgc21@gmail.com',
                'name_of_the_office_head' => 'মো: আজহারুল ইসলাম খান',
                'name_of_the_office_head_en' => 'Md. Azharul Islam Khan',
                'name_of_the_office_head_designation' => 'মহাপরিচালক',
                'name_of_the_office_head_designation_en' => 'DIRECTOR GENERAL',
                'contact_person_name' => 'Milton',
                'contact_person_name_en' => 'Milton',
                'contact_person_mobile' => '01674248402',
                'contact_person_email' => 'rahulbgc21@gmail.com',
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
            array(
                'id' => 2,
                'code' => 'SSP00000002',
                'institute_type_id' => 2,
                'title' => 'স্ট্রেন্থেনিং ইনক্লুসিভ ডেভেলপমেন্ট ইন চিটাগাং হিল ট্রাক্টস (এসআইডি - সিএইচটি)',
                'title_en' => 'Strengthening Inclusive Development in Chittagong Hill Tracts (SID-CHT)',
                'domain' => 'sid-cht.nise.gov.bd',
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => NULL,
                'location_latitude' => 23.778365382651636,
                'location_longitude' => 90.3794945399175,
                'google_map_src' => "https://www.google.com/maps/place/IDB+Bhaban,+E%2F8-A,+Dhaka+1207/@23.7781494,90.3773273,17z/data=!3m1!4b1!4m5!3m4!1s0x3755c74e8282b185:0x5e029ded49de5bfc!8m2!3d23.7781494!4d90.379516",
                'address_en' => 'UNDP, UN Offices, 18th Floor, IDB Bhaban,Agargaon, Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh',
                'address' => 'UNDP, UN Offices, 18th Floor, IDB Bhaban,Agargaon, Sher-e-Bangla Nagar, Dhaka 1207, Bangladesh',
                'logo' => NULL,
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => '+880 2 55667788',
                'phone_numbers' => '[]',
                'primary_mobile' => '01813628025',
                'mobile_numbers' => '[]',
                'email' => 'marufmazumder.piistech@gmail.com',
                'name_of_the_office_head' => 'Mr. Maruf',
                'name_of_the_office_head_en' => 'Mr. Maruf',
                'name_of_the_office_head_designation' => 'Country Head',
                'name_of_the_office_head_designation_en' => 'Country Head',
                'contact_person_name' => 'Abdur Razzak',
                'contact_person_name_en' => 'Abdur Razzak',
                'contact_person_mobile' => '01813628025',
                'contact_person_email' => 'marufmazumder.piistech@gmail.com',
                'contact_person_designation' => 'Pion',
                'contact_person_designation_en' => 'Pion',
                'config' => NULL,
                'row_status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2022-01-10 13:06:37',
                'updated_at' => '2022-01-10 14:44:00',
                'deleted_at' => NULL,
            ),
            array(
                'id' => 3,
                'code' => 'SSP00000003',
                'institute_type_id' => 1,
                'title' => 'বাংলাদেশ শিল্প কারিগরি সহায়তা কেন্দ্র (বিটাক)',
                'title_en' => 'Bangladesh Industrial Technical Assistance Center (BITAC)',
                'domain' => 'https://bitac.gov.bd',
                'loc_division_id' => 3,
                'loc_district_id' => 18,
                'loc_upazila_id' => NULL,
                'location_latitude' => 23.760964541804732,
                'location_longitude' => 90.40208418650894,
                'google_map_src' => 'https://www.google.com/maps?ll=23.760994,90.402052&z=18&t=m&hl=en-US&gl=US&mapclient=embed&cid=7485425113775247879',
                'address_en' => '১১৬ (খ), তেজগাঁও শিল্প এলাকা',
                'address' => '116 (Kha) Tejgaon Industrial Area',
                'logo' => 'https://file.nise3.xyz/uploads/L0dZRaqd8EaSeLla3PYJBwNIQsxewo1642418861.gif',
                'country' => 'BD',
                'phone_code' => '880',
                'primary_phone' => "+88-02-55030046",
                'phone_numbers' => '["+88-02-55030046", "+88-02-55030057"]',
                'primary_mobile' => '01887263793',
                'mobile_numbers' => '["01733341663"]',
                'email' => 'contact@bitac.gov.bd',
                'name_of_the_office_head' => 'জাকিয়া সুলতানা',
                'name_of_the_office_head_en' => 'Zakia Sultana',
                'name_of_the_office_head_designation' => 'মহাপরিচালক',
                'name_of_the_office_head_designation_en' => 'Director General',
                'contact_person_name' => 'আব্দুর রাজ্জাক',
                'contact_person_name_en' => 'Abdur Razzak',
                'contact_person_mobile' => '01887263793',
                'contact_person_email' => 'contact@bitac.gov.bd',
                'contact_person_designation' => 'Pion',
                'contact_person_designation_en' => 'Pion',
                'config' => NULL,
                'row_status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2022-01-16 15:54:26',
                'updated_at' => '2022-01-31 16:31:52',
                'deleted_at' => NULL,
            )
        ));

        Schema::enableForeignKeyConstraints();


    }
}