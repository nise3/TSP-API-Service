<?php

namespace App\Models;

/**
 * Class Institute
 * @package App\Models
 * @property int id
 * @property int course_id
 * @property int youth_id
 */
class CourseEnrollment extends BaseModel
{
    protected $guarded = ['id'];
}
