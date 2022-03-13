<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RTORelatedTablesSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();

        DB::table('rto_countries')->truncate();
        DB::table('rto_countries')->insert([
            array('country_id' => '2'),
            array('country_id' => '12'),
            array('country_id' => '99'),
            array('country_id' => '39'),
            array('country_id' => '38'),
            array('country_id' => '15'),
            array('country_id' => '3'),
            array('country_id' => '19'),
            array('country_id' => '234'),
            array('country_id' => '236')
        ]);

        DB::table('rpl_sectors')->truncate();
        DB::table('rpl_sectors')->insert([
            array('id' => '1','title' => 'Construction Sector','title_en' => 'Construction Sector','translations' => '{"12": {"title": "Armenian Mechanic"}, "99": {"title": "Honduras Mechanic"}}','created_at' => '2022-03-01 19:23:18','updated_at' => '2022-03-01 19:23:18','deleted_at' => NULL),
            array('id' => '2','title' => 'Information Technology','title_en' => 'Information Technology','translations' => '{"99": {"title": "IT Honduras"}}','created_at' => '2022-03-02 22:13:47','updated_at' => '2022-03-02 22:13:47','deleted_at' => NULL),
            array('id' => '3','title' => 'টেলিকমিউনিকেশন','title_en' => 'Telecommunication','translations' => '{"19": {"title": "আইটি ও টেলিকমিউনিকেশন"}, "234": {"title": "تكنولوجيا المعلومات والاتصالات"}, "236": {"title": "IT & Telecommunication"}}','created_at' => '2022-03-08 23:37:57','updated_at' => '2022-03-09 00:09:46','deleted_at' => NULL)
        ]);

        DB::table('rpl_occupations')->truncate();
        DB::table('rpl_occupations')->insert([
            array('id' => '1','rpl_sector_id' => '1','title' => 'Carpenter','title_en' => 'Carpenter','translations' => '{"12": {"title": "Armenian Carpenter"}, "99": {"title": "Honduras  Carpenter"}}','created_at' => '2022-03-01 22:48:09','updated_at' => '2022-03-01 23:51:45','deleted_at' => '2022-03-01 23:51:45'),
            array('id' => '2','rpl_sector_id' => '1','title' => 'Mason','title_en' => 'Mason','translations' => '{"12": {"title": "Armenian Mason"}, "39": {"title": "Cameroon Mason"}}','created_at' => '2022-03-01 23:51:29','updated_at' => '2022-03-01 23:51:29','deleted_at' => NULL),
            array('id' => '3','rpl_sector_id' => '1','title' => 'Plusterer','title_en' => 'Plusterer','translations' => '{"39": {"title": "Cameroon Plusterer"}, "99": {"title": "Honduras Plusterer"}}','created_at' => '2022-03-02 22:15:24','updated_at' => '2022-03-02 22:15:24','deleted_at' => NULL),
            array('id' => '4','rpl_sector_id' => '2','title' => 'Web designer','title_en' => 'Web designer','translations' => '{"39": {"title": "Cameroonian Web designer"}}','created_at' => '2022-03-02 22:16:31','updated_at' => '2022-03-02 22:16:31','deleted_at' => NULL),
            array('id' => '5','rpl_sector_id' => '2','title' => 'Web developer','title_en' => 'Web developer','translations' => '{"3": {"title": "Albanian Web developer"}, "38": {"title": "Cambodia Web developer"}}','created_at' => '2022-03-02 22:17:14','updated_at' => '2022-03-02 22:17:14','deleted_at' => NULL),
            array('id' => '6','rpl_sector_id' => '3','title' => 'Software Engineer','title_en' => 'Software Engineer','translations' => '{"19": {"title": "সফটওয়্যার ইঞ্জিনিয়ার"}, "234": {"title": "مهندس برمجيات"}}','created_at' => '2022-03-09 00:38:38','updated_at' => '2022-03-09 00:39:18','deleted_at' => NULL),
            array('id' => '7','rpl_sector_id' => '3','title' => 'Color Painter','title_en' => 'Color Painter','translations' => '{"234": {"title": "test"}}','created_at' => '2022-03-09 00:40:39','updated_at' => '2022-03-09 00:40:48','deleted_at' => '2022-03-09 00:40:48')
        ]);

        DB::table('rpl_levels')->truncate();
        DB::table('rpl_levels')->insert([
            array('id' => '1','rpl_sector_id' => '1','rpl_occupation_id' => '1','title' => 'Rpl level 1','title_en' => NULL,'translations' => '{"2": {"title": "test sector"}}','sequence_order' => '1','created_at' => '2022-03-02 00:10:26','updated_at' => '2022-03-02 00:23:38','deleted_at' => '2022-03-02 00:23:38'),
            array('id' => '2','rpl_sector_id' => '1','rpl_occupation_id' => '2','title' => 'Rpl level 1','title_en' => 'Rpl level 1','translations' => '{"12": {"title": "test"}}','sequence_order' => '1','created_at' => '2022-03-02 00:24:23','updated_at' => '2022-03-10 16:59:24','deleted_at' => NULL),
            array('id' => '3','rpl_sector_id' => '1','rpl_occupation_id' => '3','title' => 'RPL level 2','title_en' => NULL,'translations' => NULL,'sequence_order' => '2','created_at' => '2022-03-02 17:29:03','updated_at' => '2022-03-02 17:29:03','deleted_at' => NULL),
            array('id' => '4','rpl_sector_id' => '2','rpl_occupation_id' => '4','title' => 'Beginner','title_en' => NULL,'translations' => NULL,'sequence_order' => '1','created_at' => '2022-03-02 22:19:43','updated_at' => '2022-03-02 22:19:43','deleted_at' => NULL),
            array('id' => '5','rpl_sector_id' => '2','rpl_occupation_id' => '5','title' => 'Intermediate','title_en' => NULL,'translations' => NULL,'sequence_order' => '2','created_at' => '2022-03-02 22:20:15','updated_at' => '2022-03-02 22:20:15','deleted_at' => NULL),
            array('id' => '6','rpl_sector_id' => '3','rpl_occupation_id' => '6','title' => 'Level','title_en' => NULL,'translations' => NULL,'sequence_order' => '2','created_at' => '2022-03-09 00:46:18','updated_at' => '2022-03-09 00:47:09','deleted_at' => NULL),
            array('id' => '7','rpl_sector_id' => '3','rpl_occupation_id' => '7','title' => 'Level','title_en' => NULL,'translations' => NULL,'sequence_order' => '1','created_at' => '2022-03-09 00:46:58','updated_at' => '2022-03-09 00:46:58','deleted_at' => NULL)
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
