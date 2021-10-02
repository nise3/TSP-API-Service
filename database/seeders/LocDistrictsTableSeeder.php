<?php

namespace Database\Seeders;

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
            array('id' => '1','loc_division_id' => '1','title_en' => 'BARGUNA','title_bn' => 'বরগুনা','is_sadar_district' => '0','bbs_code' => '04','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '2','loc_division_id' => '1','title_en' => 'BARISAL','title_bn' => 'বরিশাল','is_sadar_district' => '1','bbs_code' => '06','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '3','loc_division_id' => '1','title_en' => 'BHOLA','title_bn' => 'ভোলা','is_sadar_district' => '0','bbs_code' => '09','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '4','loc_division_id' => '1','title_en' => 'JHALOKATI','title_bn' => 'ঝালকাঠি','is_sadar_district' => '0','bbs_code' => '42','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '5','loc_division_id' => '1','title_en' => 'PATUAKHALI','title_bn' => 'পটুয়াখালী ','is_sadar_district' => '0','bbs_code' => '78','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '6','loc_division_id' => '1','title_en' => 'PIROJPUR','title_bn' => 'পিরোজপুর ','is_sadar_district' => '0','bbs_code' => '79','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '7','loc_division_id' => '2','title_en' => 'BANDARBAN','title_bn' => 'বান্দরবান','is_sadar_district' => '0','bbs_code' => '03','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '8','loc_division_id' => '2','title_en' => 'BRAHMANBARIA','title_bn' => 'ব্রাহ্মণবাড়িয়া','is_sadar_district' => '0','bbs_code' => '12','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '9','loc_division_id' => '2','title_en' => 'CHANDPUR','title_bn' => 'চাঁদপুর','is_sadar_district' => '0','bbs_code' => '13','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '10','loc_division_id' => '2','title_en' => 'CHITTAGONG','title_bn' => 'চট্টগ্রাম','is_sadar_district' => '1','bbs_code' => '15','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '11','loc_division_id' => '2','title_en' => 'COMILLA','title_bn' => 'কুমিল্লা','is_sadar_district' => '0','bbs_code' => '19','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '12','loc_division_id' => '2','title_en' => 'COX\'S BAZAR','title_bn' => 'কক্সবাজার ','is_sadar_district' => '0','bbs_code' => '22','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '13','loc_division_id' => '2','title_en' => 'FENI','title_bn' => 'ফেনী','is_sadar_district' => '0','bbs_code' => '30','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '14','loc_division_id' => '2','title_en' => 'KHAGRACHHARI','title_bn' => 'খাগড়াছড়ি','is_sadar_district' => '0','bbs_code' => '46','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '15','loc_division_id' => '2','title_en' => 'LAKSHMIPUR','title_bn' => 'লক্ষ্মীপুর','is_sadar_district' => '0','bbs_code' => '51','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '16','loc_division_id' => '2','title_en' => 'NOAKHALI','title_bn' => 'নোয়াখালী','is_sadar_district' => '0','bbs_code' => '75','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '17','loc_division_id' => '2','title_en' => 'RANGAMATI','title_bn' => 'রাঙ্গামাটি','is_sadar_district' => '0','bbs_code' => '84','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '18','loc_division_id' => '3','title_en' => 'DHAKA','title_bn' => 'ঢাকা ','is_sadar_district' => '1','bbs_code' => '26','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '19','loc_division_id' => '3','title_en' => 'FARIDPUR','title_bn' => 'ফরিদপুর ','is_sadar_district' => '0','bbs_code' => '29','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '20','loc_division_id' => '3','title_en' => 'GAZIPUR','title_bn' => 'গাজীপুর ','is_sadar_district' => '0','bbs_code' => '33','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '21','loc_division_id' => '3','title_en' => 'GOPALGANJ','title_bn' => 'গোপালগঞ্জ','is_sadar_district' => '0','bbs_code' => '35','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '22','loc_division_id' => '8','title_en' => 'JAMALPUR','title_bn' => 'জামালপুর ','is_sadar_district' => '0','bbs_code' => '39','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '23','loc_division_id' => '3','title_en' => 'KISHOREGONJ','title_bn' => 'কিশোরগঞ্জ ','is_sadar_district' => '0','bbs_code' => '48','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '24','loc_division_id' => '3','title_en' => 'MADARIPUR','title_bn' => 'মাদারীপুর ','is_sadar_district' => '0','bbs_code' => '54','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '25','loc_division_id' => '3','title_en' => 'MANIKGANJ','title_bn' => 'মানিকগঞ্জ ','is_sadar_district' => '0','bbs_code' => '56','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '26','loc_division_id' => '3','title_en' => 'MUNSHIGANJ','title_bn' => 'মুন্সিগঞ্জ ','is_sadar_district' => '0','bbs_code' => '59','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '27','loc_division_id' => '8','title_en' => 'MYMENSINGH','title_bn' => 'ময়মনসিংহ ','is_sadar_district' => '1','bbs_code' => '61','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '28','loc_division_id' => '3','title_en' => 'NARAYANGANJ','title_bn' => 'নারায়ণগঞ্জ ','is_sadar_district' => '0','bbs_code' => '67','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '29','loc_division_id' => '3','title_en' => 'NARSINGDI','title_bn' => 'নরসিংদী ','is_sadar_district' => '0','bbs_code' => '68','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '30','loc_division_id' => '8','title_en' => 'NETRAKONA','title_bn' => 'নেত্রকোণা ','is_sadar_district' => '0','bbs_code' => '72','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '31','loc_division_id' => '3','title_en' => 'RAJBARI','title_bn' => 'রাজবাড়ী ','is_sadar_district' => '0','bbs_code' => '82','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '32','loc_division_id' => '3','title_en' => 'SHARIATPUR','title_bn' => 'শরীয়তপুর  ','is_sadar_district' => '0','bbs_code' => '86','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '33','loc_division_id' => '8','title_en' => 'SHERPUR','title_bn' => 'শেরপুর ','is_sadar_district' => '0','bbs_code' => '89','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '34','loc_division_id' => '3','title_en' => 'TANGAIL','title_bn' => 'টাঙ্গাইল ','is_sadar_district' => '0','bbs_code' => '93','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '35','loc_division_id' => '4','title_en' => 'BAGERHAT','title_bn' => 'বাগেরহাট','is_sadar_district' => '0','bbs_code' => '01','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '36','loc_division_id' => '4','title_en' => 'CHUADANGA','title_bn' => 'চুয়াডাঙ্গা ','is_sadar_district' => '0','bbs_code' => '18','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '37','loc_division_id' => '4','title_en' => 'JESSORE','title_bn' => 'যশোর','is_sadar_district' => '0','bbs_code' => '41','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '38','loc_division_id' => '4','title_en' => 'JHENAIDAH','title_bn' => 'ঝিনাইদহ ','is_sadar_district' => '0','bbs_code' => '44','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '39','loc_division_id' => '4','title_en' => 'KHULNA','title_bn' => 'খুলনা ','is_sadar_district' => '1','bbs_code' => '47','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '40','loc_division_id' => '4','title_en' => 'KUSHTIA','title_bn' => 'কুষ্টিয়া ','is_sadar_district' => '0','bbs_code' => '50','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '41','loc_division_id' => '4','title_en' => 'MAGURA','title_bn' => 'মাগুরা','is_sadar_district' => '0','bbs_code' => '55','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '42','loc_division_id' => '4','title_en' => 'MEHERPUR','title_bn' => 'মেহেরপুর ','is_sadar_district' => '0','bbs_code' => '57','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '43','loc_division_id' => '4','title_en' => 'NARAIL','title_bn' => 'নড়াইল ','is_sadar_district' => '0','bbs_code' => '65','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '44','loc_division_id' => '4','title_en' => 'SATKHIRA','title_bn' => 'সাতক্ষীরা ','is_sadar_district' => '0','bbs_code' => '87','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '45','loc_division_id' => '5','title_en' => 'BOGRA','title_bn' => 'বগুড়া ','is_sadar_district' => '0','bbs_code' => '10','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '46','loc_division_id' => '5','title_en' => 'JOYPURHAT','title_bn' => 'জয়পুরহাট','is_sadar_district' => '0','bbs_code' => '38','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '47','loc_division_id' => '5','title_en' => 'NAOGAON','title_bn' => 'নওগাঁ ','is_sadar_district' => '0','bbs_code' => '64','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '48','loc_division_id' => '5','title_en' => 'NATORE','title_bn' => 'নাটোর ','is_sadar_district' => '0','bbs_code' => '69','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '49','loc_division_id' => '5','title_en' => 'CHAPAI NABABGANJ','title_bn' => 'চাঁপাই নাবাবগঞ্জ ','is_sadar_district' => '0','bbs_code' => '70','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '50','loc_division_id' => '5','title_en' => 'PABNA','title_bn' => 'পাবনা','is_sadar_district' => '0','bbs_code' => '76','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '51','loc_division_id' => '5','title_en' => 'RAJSHAHI','title_bn' => 'রাজশাহী ','is_sadar_district' => '1','bbs_code' => '81','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '52','loc_division_id' => '5','title_en' => 'SIRAJGANJ','title_bn' => 'সিরাজগঞ্জ','is_sadar_district' => '0','bbs_code' => '88','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '53','loc_division_id' => '6','title_en' => 'DINAJPUR','title_bn' => 'দিনাজপুর ','is_sadar_district' => '0','bbs_code' => '27','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '54','loc_division_id' => '6','title_en' => 'GAIBANDHA','title_bn' => 'গাইবান্ধা ','is_sadar_district' => '0','bbs_code' => '32','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '55','loc_division_id' => '6','title_en' => 'KURIGRAM','title_bn' => 'কুড়িগ্রাম ','is_sadar_district' => '0','bbs_code' => '49','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '56','loc_division_id' => '6','title_en' => 'LALMONIRHAT','title_bn' => 'লালমনিরহাট ','is_sadar_district' => '0','bbs_code' => '52','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '57','loc_division_id' => '6','title_en' => 'NILPHAMARI','title_bn' => 'নীলফামারী','is_sadar_district' => '0','bbs_code' => '73','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '58','loc_division_id' => '6','title_en' => 'PANCHAGARH','title_bn' => 'পঞ্চগড় ','is_sadar_district' => '0','bbs_code' => '77','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '59','loc_division_id' => '6','title_en' => 'RANGPUR','title_bn' => 'রংপুর ','is_sadar_district' => '1','bbs_code' => '85','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '60','loc_division_id' => '6','title_en' => 'THAKURGAON','title_bn' => 'ঠাকুরগাঁও','is_sadar_district' => '0','bbs_code' => '94','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '61','loc_division_id' => '7','title_en' => 'HABIGANJ','title_bn' => 'হবিগঞ্জ ','is_sadar_district' => '0','bbs_code' => '36','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '62','loc_division_id' => '7','title_en' => 'MAULVIBAZAR','title_bn' => 'মৌলভীবাজার ','is_sadar_district' => '0','bbs_code' => '58','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '63','loc_division_id' => '7','title_en' => 'SUNAMGANJ','title_bn' => 'সুনামগঞ্জ ','is_sadar_district' => '0','bbs_code' => '90','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL),
            array('id' => '64','loc_division_id' => '7','title_en' => 'SYLHET','title_bn' => 'সিলেট','is_sadar_district' => '1','bbs_code' => '91','row_status' => '1','created_at' => '2020-03-29 08:07:03','updated_at' => '2020-03-29 08:07:03','deleted_at' => NULL)
        ));

        Schema::enableForeignKeyConstraints();

    }
}
