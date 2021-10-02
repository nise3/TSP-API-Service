<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocDivisionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('loc_divisions')->truncate();

        DB::table('loc_divisions')->insert(array(
            array('id' => '1','title_en' => 'Barisal','title_bn' => 'বরিশাল','bbs_code' => '10','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '2','title_en' => 'Chittagong','title_bn' => 'চট্টগ্রাম','bbs_code' => '20','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '3','title_en' => 'Dhaka','title_bn' => 'ঢাকা','bbs_code' => '30','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '4','title_en' => 'Khulna','title_bn' => 'খুলনা','bbs_code' => '40','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '5','title_en' => 'Rajshahi','title_bn' => 'রাজশাহী','bbs_code' => '50','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '6','title_en' => 'Rangpur','title_bn' => 'রংপুর','bbs_code' => '60','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '7','title_en' => 'Sylhet','title_bn' => 'সিলেট','bbs_code' => '70','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL),
            array('id' => '8','title_en' => 'Mymensingh','title_bn' => 'ময়মনসিংহ','bbs_code' => '45','row_status' => '1','created_at' => '2015-11-17 12:01:41','updated_at' => '2016-02-09 20:06:15','deleted_at' => NULL)
        ));

        Schema::enableForeignKeyConstraints();

    }
}
