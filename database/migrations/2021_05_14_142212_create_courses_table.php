<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 150);
            $table->unsignedInteger('institute_id');
            $table->unsignedTinyInteger('level')->comment("1=Beginner,2=>Intermediate,3=>Expert");
            $table->unsignedTinyInteger('language_medium')->comment("1=Bengali,2=>English");
            $table->unsignedInteger('branch_id')
                ->nullable();
            $table->unsignedInteger('program_id')
                ->nullable();
            $table->string('title', 1000);
            $table->string('title_en', 500)->nullable();
            $table->float('course_fee')->default(0);
            $table->float('duration')->nullable()
                ->comment('Duration in hours');
            $table->text('overview')->nullable();
            $table->text('overview_en')->nullable();
            $table->string('target_group', 1000)
                ->nullable();
            $table->string('target_group_en', 500)
                ->nullable();
            $table->text('objectives')->nullable();
            $table->text('objectives_en')->nullable();
            $table->text('lessons')->nullable();
            $table->text('lessons_en')->nullable();
            $table->string('training_methodology', 600)
                ->nullable();
            $table->string('training_methodology_en', 1000)
                ->nullable();
            $table->string('evaluation_system', 1000)
                ->nullable();
            $table->string('evaluation_system_en', 500)
                ->nullable();

            $table->text('prerequisite')->nullable();
            $table->text('prerequisite_en')->nullable();
            $table->text('eligibility')->nullable();
            $table->text('eligibility_en')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->json('application_form_settings')->nullable();
            $table->unsignedTinyInteger('row_status')
                ->default(1);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('courses');
    }
}
