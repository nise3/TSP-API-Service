<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read  Collection youths
 */
class Skill extends BaseModel
{
    use HasFactory;

    public $timestamps = false;
    /**
     * @var string[]
     */
    protected $guarded = ['id'];
    /**
     * @var string[]
     */
    protected $hidden = ["pivot"];

}
