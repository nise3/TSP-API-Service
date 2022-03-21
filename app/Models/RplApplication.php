<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RplApplication extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    protected $table = "rpl_applications";
    protected $casts = [
        'youth_details' => 'array'
    ];

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const NID = 1;
    public const BIRTH_CARD = 2;
    public const PASSPORT = 3;
    public const IDENTITY_TYPES = [
        self::NID,
        self::BIRTH_CARD,
        self::PASSPORT
    ];


    public const IS_YOUTH_CURRENTLY_WORKING_TRUE = 1;
    public const IS_YOUTH_CURRENTLY_WORKING_FALSE = 0;


    public const IS_YOUTH_CURRENTLY_WORKING = [
        self::IS_YOUTH_CURRENTLY_WORKING_TRUE,
        self::IS_YOUTH_CURRENTLY_WORKING_FALSE
    ];


    public const RELIGION_ISLAM = 1;
    public const RELIGION_HINDUISM = 2;
    public const RELIGION_CHRISTIANITY = 3;
    public const RELIGION_BUDDHISM = 4;
    public const RELIGION_JUDAISM = 5;
    public const RELIGION_SIKHISM = 6;
    public const RELIGION_ETHNIC = 7;
    public const RELIGION_AGNOSTIC_ATHEIST = 8;
    public const RELIGIONS = [
        self::RELIGION_ISLAM,
        self::RELIGION_HINDUISM,
        self::RELIGION_CHRISTIANITY,
        self::RELIGION_BUDDHISM,
        self::RELIGION_JUDAISM,
        self::RELIGION_SIKHISM,
        self::RELIGION_ETHNIC,
        self::RELIGION_AGNOSTIC_ATHEIST
    ];

    public const APPLICATION_STATUS_ASSESSMENT_SUBMITTED = 1;
    public const APPLICATION_STATUS_APPLICATION_SUBMITTED = 2;
    public const APPLICATION_STATUS_PAYMENT_COMPLETED = 3;
    public const APPLICATION_STATUS_ASSIGNED_TO_BATCH = 4;
    public const APPLICATION_STATUS_ASSESSMENT_COMPLETED = 5;

    public const RPL_APPLICATION_STATUS = [
        self::APPLICATION_STATUS_ASSESSMENT_SUBMITTED,
        self::APPLICATION_STATUS_APPLICATION_SUBMITTED,
        self::APPLICATION_STATUS_PAYMENT_COMPLETED,
        self::APPLICATION_STATUS_ASSIGNED_TO_BATCH,
        self::APPLICATION_STATUS_ASSESSMENT_COMPLETED,
    ];


    /**
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(RplApplicationAddress::class, 'rpl_application_id')
            ->leftJoin('loc_divisions', 'loc_divisions.id', '=', 'rpl_application_addresses.loc_division_id')
            ->leftJoin('loc_districts', 'loc_districts.id', '=', 'rpl_application_addresses.loc_district_id')
            ->leftJoin('loc_upazilas', 'loc_upazilas.id', '=', 'rpl_application_addresses.loc_upazila_id')
            ->select(['rpl_application_addresses.*',
                'loc_divisions.title as loc_division_title',
                'loc_divisions.title_en as loc_division_title_en',
                'loc_districts.title as loc_district_title',
                'loc_districts.title_en as loc_district_title_en',
                'loc_upazilas.title as loc_upazila_title',
                'loc_upazilas.title_en as loc_upazila_tile_en']);
    }


    public function educations(): HasMany
    {
        return $this->hasMany(RplApplicationEducation::class, 'rpl_application_id')
            ->leftJoin('exam_degrees', 'exam_degrees.id', '=', 'rpl_application_education.exam_degree_id')
            ->leftJoin('edu_groups', 'edu_groups.id', '=', 'rpl_application_education.edu_group_id')
            ->leftJoin('edu_boards', 'edu_boards.id', '=', 'rpl_application_education.edu_board_id')
            ->leftJoin('education_levels', 'education_levels.id', '=', 'rpl_application_education.education_level_id')
            ->select(['rpl_application_education.*',
                'exam_degrees.title as exam_degree_title',
                'exam_degrees.title_en as exam_degree_title_en',
                'edu_groups.title as edu_group_title',
                'edu_groups.title_en as edu_group_title_en',
                'edu_boards.title as edu_board_title',
                'edu_boards.title_en as edu_board_title_en',
                'education_levels.title as education_level_title',
                'education_levels.title_en as education_level_title_en',
            ]);
    }

    /**
     * @return HasMany
     */
    public function professionalQualifications(): HasMany
    {
        return $this->hasMany(RplApplicationProfessionalQualification::class, 'rpl_application_id')
            ->leftJoin('rto_countries', 'rto_countries.country_id', '=', 'rpl_application_professional_qualifications.rto_country_id')
            ->leftJoin('rpl_sectors', 'rpl_sectors.id', '=', 'rpl_application_professional_qualifications.rpl_sector_id')
            ->leftJoin('rpl_occupations', 'rpl_occupations.id', '=', 'rpl_application_professional_qualifications.rpl_occupation_id')
            ->leftJoin('rpl_levels', 'rpl_levels.id', '=', 'rpl_application_professional_qualifications.rpl_level_id')
            ->select(['rpl_application_professional_qualifications.*',
                'rpl_levels.title as rpl_level_title',
                'rpl_levels.title_en as rpl_level_title_en',
                'rpl_occupations.title as rpl_occupation_title',
                'rpl_occupations.title_en as rpl_occupation_title_en',
                'rpl_sectors.title as rpl_sector_title',
                'rpl_sectors.title_en as rpl_sector_title_en',
            ]);

    }
}
