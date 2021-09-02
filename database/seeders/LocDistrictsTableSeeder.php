<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocDistrictsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();

        DB::table('loc_districts')->truncate();

        DB::table('loc_districts')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'loc_division_id' => 1,
                    'division_bbs_code' => '10',
                    'title_en' => 'BARGUNA',
                    'title_bn' => 'বরগুনা',
                    'bbs_code' => '04',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            1 =>
                array(
                    'id' => 2,
                    'loc_division_id' => 1,
                    'division_bbs_code' => '10',
                    'title_en' => 'BARISAL',
                    'title_bn' => 'বরিশাল',
                    'bbs_code' => '06',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            2 =>
                array(
                    'id' => 3,
                    'loc_division_id' => 1,
                    'division_bbs_code' => '10',
                    'title_en' => 'BHOLA',
                    'title_bn' => 'ভোলা',
                    'bbs_code' => '09',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            3 =>
                array(
                    'id' => 4,
                    'loc_division_id' => 1,
                    'division_bbs_code' => '10',
                    'title_en' => 'JHALOKATI',
                    'title_bn' => 'ঝালকাঠি',
                    'bbs_code' => '42',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            4 =>
                array(
                    'id' => 5,
                    'loc_division_id' => 1,
                    'division_bbs_code' => '10',
                    'title_en' => 'PATUAKHALI',
                    'title_bn' => 'পটুয়াখালী ',
                    'bbs_code' => '78',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            5 =>
                array(
                    'id' => 6,
                    'loc_division_id' => 1,
                    'division_bbs_code' => '10',
                    'title_en' => 'PIROJPUR',
                    'title_bn' => 'পিরোজপুর ',
                    'bbs_code' => '79',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            6 =>
                array(
                    'id' => 7,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'BANDARBAN',
                    'title_bn' => 'বান্দরবান',
                    'bbs_code' => '03',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            7 =>
                array(
                    'id' => 8,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'BRAHMANBARIA',
                    'title_bn' => 'ব্রাহ্মণবাড়িয়া',
                    'bbs_code' => '12',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            8 =>
                array(
                    'id' => 9,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'CHANDPUR',
                    'title_bn' => 'চাঁদপুর',
                    'bbs_code' => '13',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            9 =>
                array(
                    'id' => 10,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'CHITTAGONG',
                    'title_bn' => 'চট্টগ্রাম',
                    'bbs_code' => '15',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            10 =>
                array(
                    'id' => 11,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'COMILLA',
                    'title_bn' => 'কুমিল্লা',
                    'bbs_code' => '19',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            11 =>
                array(
                    'id' => 12,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'COX\'S BAZAR',
                    'title_bn' => 'কক্সবাজার ',
                    'bbs_code' => '22',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            12 =>
                array(
                    'id' => 13,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'FENI',
                    'title_bn' => 'ফেনী',
                    'bbs_code' => '30',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            13 =>
                array(
                    'id' => 14,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'KHAGRACHHARI',
                    'title_bn' => 'খাগড়াছড়ি',
                    'bbs_code' => '46',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            14 =>
                array(
                    'id' => 15,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'LAKSHMIPUR',
                    'title_bn' => 'লক্ষ্মীপুর',
                    'bbs_code' => '51',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            15 =>
                array(
                    'id' => 16,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'NOAKHALI',
                    'title_bn' => 'নোয়াখালী',
                    'bbs_code' => '75',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            16 =>
                array(
                    'id' => 17,
                    'loc_division_id' => 2,
                    'division_bbs_code' => '20',
                    'title_en' => 'RANGAMATI',
                    'title_bn' => 'রাঙ্গামাটি',
                    'bbs_code' => '84',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            17 =>
                array(
                    'id' => 18,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'DHAKA',
                    'title_bn' => 'ঢাকা ',
                    'bbs_code' => '26',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            18 =>
                array(
                    'id' => 19,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'FARIDPUR',
                    'title_bn' => 'ফরিদপুর ',
                    'bbs_code' => '29',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            19 =>
                array(
                    'id' => 20,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'GAZIPUR',
                    'title_bn' => 'গাজীপুর ',
                    'bbs_code' => '33',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            20 =>
                array(
                    'id' => 21,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'GOPALGANJ',
                    'title_bn' => 'গোপালগঞ্জ',
                    'bbs_code' => '35',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            21 =>
                array(
                    'id' => 22,
                    'loc_division_id' => 9,
                    'division_bbs_code' => '45',
                    'title_en' => 'JAMALPUR',
                    'title_bn' => 'জামালপুর ',
                    'bbs_code' => '39',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => '2016-02-09 14:08:46',
                ),
            22 =>
                array(
                    'id' => 23,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'KISHOREGONJ',
                    'title_bn' => 'কিশোরগঞ্জ ',
                    'bbs_code' => '48',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            23 =>
                array(
                    'id' => 24,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'MADARIPUR',
                    'title_bn' => 'মাদারীপুর ',
                    'bbs_code' => '54',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            24 =>
                array(
                    'id' => 25,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'MANIKGANJ',
                    'title_bn' => 'মানিকগঞ্জ ',
                    'bbs_code' => '56',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            25 =>
                array(
                    'id' => 26,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'MUNSHIGANJ',
                    'title_bn' => 'মুন্সিগঞ্জ ',
                    'bbs_code' => '59',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            26 =>
                array(
                    'id' => 27,
                    'loc_division_id' => 9,
                    'division_bbs_code' => '45',
                    'title_en' => 'MYMENSINGH',
                    'title_bn' => 'ময়মনসিংহ ',
                    'bbs_code' => '61',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            27 =>
                array(
                    'id' => 28,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'NARAYANGANJ',
                    'title_bn' => 'নারায়ণগঞ্জ ',
                    'bbs_code' => '67',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            28 =>
                array(
                    'id' => 29,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'NARSINGDI',
                    'title_bn' => 'নরসিংদী ',
                    'bbs_code' => '68',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            29 =>
                array(
                    'id' => 30,
                    'loc_division_id' => 9,
                    'division_bbs_code' => '45',
                    'title_en' => 'NETRAKONA',
                    'title_bn' => 'নেত্রকোণা ',
                    'bbs_code' => '72',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => '2016-02-09 19:31:29',
                ),
            30 =>
                array(
                    'id' => 31,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'RAJBARI',
                    'title_bn' => 'রাজবাড়ী ',
                    'bbs_code' => '82',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            31 =>
                array(
                    'id' => 32,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'SHARIATPUR',
                    'title_bn' => 'শরীয়তপুর  ',
                    'bbs_code' => '86',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            32 =>
                array(
                    'id' => 33,
                    'loc_division_id' => 9,
                    'division_bbs_code' => '45',
                    'title_en' => 'SHERPUR',
                    'title_bn' => 'শেরপুর ',
                    'bbs_code' => '89',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => '2016-02-09 19:31:55',
                ),
            33 =>
                array(
                    'id' => 34,
                    'loc_division_id' => 3,
                    'division_bbs_code' => '30',
                    'title_en' => 'TANGAIL',
                    'title_bn' => 'টাঙ্গাইল ',
                    'bbs_code' => '93',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            34 =>
                array(
                    'id' => 35,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'BAGERHAT',
                    'title_bn' => 'বাগেরহাট',
                    'bbs_code' => '01',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            35 =>
                array(
                    'id' => 36,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'CHUADANGA',
                    'title_bn' => 'চুয়াডাঙ্গা ',
                    'bbs_code' => '18',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            36 =>
                array(
                    'id' => 37,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'JESSORE',
                    'title_bn' => 'যশোর ',
                    'bbs_code' => '41',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            37 =>
                array(
                    'id' => 38,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'JHENAIDAH',
                    'title_bn' => 'ঝিনাইদহ ',
                    'bbs_code' => '44',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            38 =>
                array(
                    'id' => 39,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'KHULNA',
                    'title_bn' => 'খুলনা ',
                    'bbs_code' => '47',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            39 =>
                array(
                    'id' => 40,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'KUSHTIA',
                    'title_bn' => 'কুষ্টিয়া ',
                    'bbs_code' => '50',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            40 =>
                array(
                    'id' => 41,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'MAGURA',
                    'title_bn' => 'মাগুরা',
                    'bbs_code' => '55',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            41 =>
                array(
                    'id' => 42,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'MEHERPUR',
                    'title_bn' => 'মেহেরপুর ',
                    'bbs_code' => '57',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            42 =>
                array(
                    'id' => 43,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'NARAIL',
                    'title_bn' => 'নড়াইল ',
                    'bbs_code' => '65',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            43 =>
                array(
                    'id' => 44,
                    'loc_division_id' => 4,
                    'division_bbs_code' => '40',
                    'title_en' => 'SATKHIRA',
                    'title_bn' => 'সাতক্ষীরা ',
                    'bbs_code' => '87',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            44 =>
                array(
                    'id' => 45,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'BOGRA',
                    'title_bn' => 'বগুড়া ',
                    'bbs_code' => '10',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            45 =>
                array(
                    'id' => 46,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'JOYPURHAT',
                    'title_bn' => 'জয়পুরহাট',
                    'bbs_code' => '38',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            46 =>
                array(
                    'id' => 47,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'NAOGAON',
                    'title_bn' => 'নওগাঁ ',
                    'bbs_code' => '64',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            47 =>
                array(
                    'id' => 48,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'NATORE',
                    'title_bn' => 'নাটোর ',
                    'bbs_code' => '69',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            48 =>
                array(
                    'id' => 49,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'CHAPAI NABABGANJ',
                    'title_bn' => 'চাঁপাই নাবাবগঞ্জ ',
                    'bbs_code' => '70',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            49 =>
                array(
                    'id' => 50,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'PABNA',
                    'title_bn' => 'পাবনা',
                    'bbs_code' => '76',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            50 =>
                array(
                    'id' => 51,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'RAJSHAHI',
                    'title_bn' => 'রাজশাহী ',
                    'bbs_code' => '81',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            51 =>
                array(
                    'id' => 52,
                    'loc_division_id' => 5,
                    'division_bbs_code' => '50',
                    'title_en' => 'SIRAJGANJ',
                    'title_bn' => 'সিরাজগঞ্জ',
                    'bbs_code' => '88',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            52 =>
                array(
                    'id' => 53,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'DINAJPUR',
                    'title_bn' => 'দিনাজপুর ',
                    'bbs_code' => '27',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            53 =>
                array(
                    'id' => 54,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'GAIBANDHA',
                    'title_bn' => 'গাইবান্ধা ',
                    'bbs_code' => '32',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            54 =>
                array(
                    'id' => 55,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'KURIGRAM',
                    'title_bn' => 'কুড়িগ্রাম ',
                    'bbs_code' => '49',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            55 =>
                array(
                    'id' => 56,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'LALMONIRHAT',
                    'title_bn' => 'লালমনিরহাট ',
                    'bbs_code' => '52',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            56 =>
                array(
                    'id' => 57,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'NILPHAMARI',
                    'title_bn' => 'নীলফামারী',
                    'bbs_code' => '73',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            57 =>
                array(
                    'id' => 58,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'PANCHAGARH',
                    'title_bn' => 'পঞ্চগড় ',
                    'bbs_code' => '77',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            58 =>
                array(
                    'id' => 59,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'RANGPUR',
                    'title_bn' => 'রংপুর ',
                    'bbs_code' => '85',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            59 =>
                array(
                    'id' => 60,
                    'loc_division_id' => 6,
                    'division_bbs_code' => '60',
                    'title_en' => 'THAKURGAON',
                    'title_bn' => 'ঠাকুরগাঁও',
                    'bbs_code' => '94',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            60 =>
                array(
                    'id' => 61,
                    'loc_division_id' => 7,
                    'division_bbs_code' => '70',
                    'title_en' => 'HABIGANJ',
                    'title_bn' => 'হবিগঞ্জ ',
                    'bbs_code' => '36',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            61 =>
                array(
                    'id' => 62,
                    'loc_division_id' => 7,
                    'division_bbs_code' => '70',
                    'title_en' => 'MAULVIBAZAR',
                    'title_bn' => 'মৌলভীবাজার ',
                    'bbs_code' => '58',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            62 =>
                array(
                    'id' => 63,
                    'loc_division_id' => 7,
                    'division_bbs_code' => '70',
                    'title_en' => 'SUNAMGANJ',
                    'title_bn' => 'সুনামগঞ্জ ',
                    'bbs_code' => '90',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
            63 =>
                array(
                    'id' => 64,
                    'loc_division_id' => 7,
                    'division_bbs_code' => '70',
                    'title_en' => 'SYLHET',
                    'title_bn' => 'সিলেট',
                    'bbs_code' => '91',
                    'row_status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                   'created_at'=>Carbon::now(),
                    'updated_at' => Carbon::now()
                ),
        ));

        Schema::enableForeignKeyConstraints();
    }
}
