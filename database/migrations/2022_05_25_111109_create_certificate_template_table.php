<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_template', function (Blueprint $table) {
            $table->increments('id');
            $table->text('template');
            $table->string('title',500);
            $table->string('title_en',300);
            $table->unsignedInteger('result_type')->comment("1=>Competent, 2=>Not Competent, 3=> Grading, 4=>Marks, 5=>Participation");
            $table->string('accessor_type', 100);
            $table->unsignedInteger('accessor_id');
            $table->unsignedTinyInteger('language')
                ->nullable()
                ->comment('1 => Bangla, 2 => English')
                ->default(1);
            $table->timestamp('issued_at')->nullable();
            $table->unsignedTinyInteger('row_status')->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificate_template');
    }
}
