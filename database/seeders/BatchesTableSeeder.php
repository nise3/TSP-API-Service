<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BatchesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();

        DB::table('batches')->truncate();

        Schema::enableForeignKeyConstraints();

    }
}
