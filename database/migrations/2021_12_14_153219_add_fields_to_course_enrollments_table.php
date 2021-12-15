<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCourseEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->string("verification_code", 10)->after('signature_image_path')->nullable()
                ->comment('SMS verification code');
            $table->dateTime("verification_code_sent_at")->nullable()
                ->comment('SMS verification code sent at');
            $table->dateTime("verification_code_verified_at")->nullable()
                ->comment('SMS verification code verified at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropColumn('verification_code');
            $table->dropColumn('verification_code_sent_at');
            $table->dropColumn('verification_code_verified_at');
            $table->dropColumn('payment_status');
        });
    }
}
