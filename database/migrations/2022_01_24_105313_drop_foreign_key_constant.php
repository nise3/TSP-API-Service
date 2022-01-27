<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyConstant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign('courses_fk_institute_id');
            $table->dropForeign('courses_fk_program_id');
        });
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign('branches_fk_institute_id');
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign('programs_fk_institute_id');
        });
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign('batches_fk_branch_id');
            $table->dropForeign('batches_fk_course_id');
            $table->dropForeign('batches_fk_institute_id');
            $table->dropForeign('batches_fk_training_center_id');
        });
        Schema::table('training_centers', function (Blueprint $table) {
            $table->dropForeign('training_centers_fk_branch_id');
            $table->dropForeign('training_centers_fk_institute_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_centers', function (Blueprint $table) {
           //
        });
    }
}
