<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EnrollmentEducation
 *
 * @property int id
 * @property int youth_id
 * @property int level_of_education
 * @property int exam_degree_id
 * @property int|null edu_group_id
 * @property int|null edu_board_id
 * @property int|null is_foreign_institute
 * @property int|null foreign_institute_country_id
 * @property int result
 * @property int|null duration
 * @property string|null exam_degree_name
 * @property string|null exam_degree_name_en
 * @property string|null major_or_concentration
 * @property string|null major_or_concentration_en
 * @property string|null institute_name_en
 * @property float|null marks_in_percentage
 * @property float|null cgpa_scale
 * @property float|null cgpa
 * @property string year_of_passing
 * @property string|null achievements
 * @property string|null achievements_en
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EnrollmentEducation extends BaseModel
{
    use SoftDeletes;

    protected $table = "enrollment_educations"; //dont remove this

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    /**  CGPA SCALE */
    const GPA_OUT_OF_FIVE = 5;
    const GPA_OUT_OF_FOUR = 4;

    /** Education Attributes Key */
    public const DEGREE = "DEGREE";
    public const BOARD = "BOARD";
    public const MAJOR = "MAJOR";
    public const EXAM_DEGREE_NAME = "EXAM_DEGREE_NAME";
    public const MARKS = "MARKS";
    public const CGPA = "CGPA";
    public const SCALE = "SCALE";
    public const YEAR_OF_PASS = "YEAR_OF_PASS";
    public const EXPECTED_YEAR_OF_PASS = "EXPECTED_YEAR_OF_PASS";
    public const EXPECTED_YEAR_OF_EXPERIENCE = "EXPECTED_YEAR_OF_EXPERIENCE";
    public const EDU_GROUP = "EDU_GROUP";

    /** Trigger Flag For Education Form Validation */
    public const EDUCATION_LEVEL_TRIGGER = "EDUCATION_LEVEL";
    public const RESULT_TRIGGER = "RESULT";
}
