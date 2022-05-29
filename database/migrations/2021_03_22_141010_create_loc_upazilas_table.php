<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocUpazilasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loc_upazilas', function(Blueprint $table)
		{
			$table->mediumIncrements('id');
			$table->unsignedMediumInteger('loc_division_id');
			$table->unsignedMediumInteger('loc_district_id');
            $table->string('title', 500);
            $table->string('title_en');
			$table->unsignedTinyInteger('is_sadar_upazila')->default(0);
			$table->char('bbs_code', 6)->nullable();
            $table->softDeletes();

        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('loc_upazilas');
	}

}
