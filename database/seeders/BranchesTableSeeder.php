<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BranchesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        Schema::disableForeignKeyConstraints();

        DB::table('branches')->truncate();
        
        \DB::table('branches')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => '765786576',
                'institute_id' => 26,
                'title' => 'branch-1',
                'title_en' => NULL,
                'loc_division_id' => NULL,
                'loc_district_id' => NULL,
                'loc_upazila_id' => NULL,
                'location_latitude' => NULL,
                'location_longitude' => NULL,
                'google_map_src' => NULL,
                'address' => NULL,
                'address_en' => NULL,
                'row_status' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-01-23 15:12:19',
                'updated_at' => '2022-01-23 15:12:19',
                'deleted_at' => NULL,
            ),
        ));

        Schema::enableForeignKeyConstraints();

        
    }
}