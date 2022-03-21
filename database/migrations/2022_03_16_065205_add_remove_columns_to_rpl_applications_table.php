<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemoveColumnsToRplApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rpl_applications', function (Blueprint $table) {

            $table->string('registration_number', 150)->after('rpl_level_id')->nullable();

            $table->string('first_name', 300)->after('youth_id');
            $table->string('first_name_en', 150)->after('first_name')->nullable();
            $table->string('last_name', 300)->after('first_name_en');
            $table->string('last_name_en', 150)->after('last_name')->nullable();

            $table->date('date_of_birth')->after('last_name_en');

            $table->string('father_name', 500)->after('date_of_birth');
            $table->string('father_name_en', 250)->after('father_name')->nullable();
            $table->string('mother_name', 500)->after('father_name_en');
            $table->string('mother_name_en', 250)->after('mother_name')->nullable();

            $table->unsignedTinyInteger("is_currently_working")->after('mother_name_en')
                ->comment("1=>Yes,0=>No")->default(0);
            $table->string("company_name", 600)->after('is_currently_working')->nullable();
            $table->string("company_name_en", 300)->after('is_currently_working')->nullable();
            $table->string("position", 300)->after('company_name_en')->nullable();
            $table->string("position_en", 150)->after('position')->nullable();

            $table->unsignedSmallInteger('nationality')->after('position_en')->default(1);
            $table->string('mobile', 20)->after('position_en')->nullable();
            $table->unsignedTinyInteger('religion')->after('mobile')
                ->comment('1 => Islam, 2 => Hinduism, 3 => Christianity, 4 => Buddhism, 5 => Judaism, 6 => Sikhism, 7 =>Ethnic,8=>Agnostic/Atheist');

            $table->unsignedTinyInteger('identity_number_type')->after('religion')
                ->nullable()->comment('Nid => 1, Birth Cert => 2, Passport => 3');
            $table->string('identity_number', 100)->after('identity_number_type')->nullable();
            $table->string('photo', 600)->after('identity_number')->nullable();


            $table->dropColumn('youth_details');
            /** Drop column youth_details */
            $table->unsignedInteger('rto_batch_id')->nullable()->change();
            /** Remove index from rto_batch_id */

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rpl_applications', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('first_name_en');
            $table->dropColumn('last_name');
            $table->dropColumn('last_name_en');
            $table->dropColumn('father_name');
            $table->dropColumn('father_name_en');
            $table->dropColumn('mother_name');
            $table->dropColumn('mother_name_en');
            $table->dropColumn('date_of_birth');
            $table->dropColumn("is_currently_working");
            $table->dropColumn("company_name");
            $table->dropColumn("company_name_en");
            $table->dropColumn("position");
            $table->dropColumn("position_en");
            $table->dropColumn('mobile');
            $table->dropColumn('religion');
            $table->dropColumn('identity_number_type');
            $table->dropColumn('identity_number');
            $table->json('youth_details')->after('youth_id');
        });
    }
}
