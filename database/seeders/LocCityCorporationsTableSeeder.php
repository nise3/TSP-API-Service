<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocCityCorporationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('loc_city_corporations')->truncate();

        DB::table('loc_city_corporations')->insert(array(
            array('id' => '3', 'title' => 'ঢাকা দক্ষিন সিটি কর্পোরেশন', 'title_en' => 'Dhaka South City Corporation', 'loc_division_id' => '3', 'loc_district_id' => '18', 'deleted_at' => NULL),
            array('id' => '4', 'title' => 'ঢাকা উত্তর সিটি কর্পোরেশন', 'title_en' => 'Dhaka North City Corporation ', 'loc_division_id' => '3', 'loc_district_id' => '18', 'deleted_at' => NULL),
            array('id' => '5', 'title' => 'খুলনা সিটি কর্পোরেশন', 'title_en' => 'Khulna City Corporation', 'loc_division_id' => '4', 'loc_district_id' => '39', 'deleted_at' => NULL),
            array('id' => '6', 'title' => 'রংপুর সিটি কর্পোরেশন', 'title_en' => 'Rangpur City Corporation', 'loc_division_id' => '6', 'loc_district_id' => '59', 'deleted_at' => NULL),
            array('id' => '7', 'title' => 'কুমিল্লা সিটি কর্পোরেশন', 'title_en' => 'Comilla City Corporation', 'loc_division_id' => '2', 'loc_district_id' => '11', 'deleted_at' => NULL),
            array('id' => '8', 'title' => 'চট্রগ্রাম সিটি কর্পোরেশন', 'title_en' => 'Chittagong City Corporation', 'loc_division_id' => '2', 'loc_district_id' => '10', 'deleted_at' => NULL),
            array('id' => '9', 'title' => 'নারায়ণগঞ্জ সিটি কর্পোরেশন', 'title_en' => 'Narayangonj City Corporation', 'loc_division_id' => '3', 'loc_district_id' => '28', 'deleted_at' => NULL),
            array('id' => '10', 'title' => 'সিলেট সিটি কর্পোরেশন', 'title_en' => 'Sylhet City Corporation', 'loc_division_id' => '7', 'loc_district_id' => '64', 'deleted_at' => NULL),
            array('id' => '11', 'title' => 'গাজীপুর সিটি কর্পোরেশন', 'title_en' => 'Gazipur City Corporation', 'loc_division_id' => '3', 'loc_district_id' => '20', 'deleted_at' => NULL),
            array('id' => '12', 'title' => 'রাজশাহী সিটি কর্পোরেশন', 'title_en' => 'Rajshahi City Corporation', 'loc_division_id' => '5', 'loc_district_id' => '51', 'deleted_at' => NULL),
            array('id' => '13', 'title' => 'বরিশাল সিটি কর্পোরেশন', 'title_en' => 'Barisal City Corporation', 'loc_division_id' => '1', 'loc_district_id' => '2', 'deleted_at' => NULL)
        ));


        Schema::enableForeignKeyConstraints();

    }
}
