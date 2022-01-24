<?php

namespace Database\Seeders;

use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        CourseEnrollment::query()->truncate();
        $courses = Course::all();
        foreach ($courses as $course) {
            for ($i = 1; $i < 5; $i++) {
                /** @var CourseEnrollment $courseEnrollment */
                $courseEnrollment = app(CourseEnrollment::class);
                $courseEnrollment->institute_id = $course->institute_id;
                $courseEnrollment->course_id = $course->id;
                $courseEnrollment->program_id = $course->program_id;
                $courseEnrollment->youth_id = $i;
                $youthCodeSize = 16 - strlen($i);
                $courseEnrollment->youth_code = str_pad("Y", $youthCodeSize, "0") . $i;
                $courseEnrollment->first_name = Str::random(10);
                $courseEnrollment->last_name = Str::random(10);
                $courseEnrollment->email = Str::random(10) . "@gmail.com";
                $mobileNumberLength = 11 - strlen($i);
                $courseEnrollment->mobile = str_pad("0170" . $i, $mobileNumberLength, "0") . $i;
                $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_COMMIT;
                $courseEnrollment->save();
            }
        }
        Schema::disableForeignKeyConstraints();
    }
}
