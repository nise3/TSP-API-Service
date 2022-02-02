<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SspPessimisticLockingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        Schema::disableForeignKeyConstraints();

        DB::table('ssp_pessimistic_lockings')->truncate();
        

        Schema::enableForeignKeyConstraints();

        
    }
}