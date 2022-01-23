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
            array('id' => '1','title_en' => 'Barisal','title' => 'বরিশাল','bbs_code' => '10','deleted_at' => NULL),
            array('id' => '2','title_en' => 'Chittagong','title' => 'চট্টগ্রাম','bbs_code' => '20','deleted_at' => NULL),
            array('id' => '3','title_en' => 'Dhaka','title' => 'ঢাকা','bbs_code' => '30','deleted_at' => NULL),
            array('id' => '4','title_en' => 'Khulna','title' => 'খুলনা','bbs_code' => '40','deleted_at' => NULL),
            array('id' => '5','title_en' => 'Rajshahi','title' => 'রাজশাহী','bbs_code' => '50','deleted_at' => NULL),
            array('id' => '6','title_en' => 'Rangpur','title' => 'রংপুর','bbs_code' => '60','deleted_at' => NULL),
            array('id' => '7','title_en' => 'Sylhet','title' => 'সিলেট','bbs_code' => '70','deleted_at' => NULL),
            array('id' => '8','title_en' => 'Mymensingh','title' => 'ময়মনসিংহ','bbs_code' => '45','deleted_at' => NULL)
        ));

        Schema::enableForeignKeyConstraints();

    }
}
