<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use App\Models\EducationLevel;
use App\Models\EnrollmentEducation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseEnrollmentService
{

    public function enrollCourse(array $data): CourseEnrollment {
        $courseEnrollment = app(CourseEnrollment::class);
        $courseEnrollment->fill($data);
        $courseEnrollment->save();

        return $courseEnrollment;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function courseEnrollmentValidator(Request $request, int $id = null): Validator
    {
        $rules = [
            'youth_id' => [
                'required',
                'int',
                'min:1'
            ],
            'first_name' => [
                'required',
                'string',
                'max:300'
            ],
            'first_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'last_name' => [
                'required',
                'string',
                'max:300'
            ],
            'last_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'program_id' => [
                'nullable',
                'exists:programs,id,deleted_at,NULL',
                'int'
            ],
            'course_id' => [
                'required',
                'exists:courses,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'training_center_id' => [
                'required',
                'exists:training_centers,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'batch_id' => [
                'required',
                'exists:batches,id,deleted_at,NULL',
                'int',
                'min:1'
            ],
            'gender' => [
                'required',
                Rule::in(BaseModel::GENDERS),
                'int',
            ],
            'date_of_birth' => [
                'required',
                'date',
            ],
//            'email' => [
//                'required',
//                'email',
//            ],
//            "mobile" => [
//                "required",
//                "max:11",
//                BaseModel::MOBILE_REGEX
//            ],
            'marital_status' => [
                'required',
                'int',
                Rule::in(CourseEnrollment::MARITAL_STATUSES)
            ],
            'religion' => [
                'required',
                'int',
                Rule::in(CourseEnrollment::RELIGIONS)
            ],
            'nationality' => [
                'int',
                'required'
            ],
            'does_belong_to_ethnic_group' => [
                'int',
                'required'
            ],
            'identity_number_type' => [
                'int',
                'nullable',
                Rule::in(CourseEnrollment::IDENTITY_TYPES)
            ],
            'identity_number' => [
                'string',
                'nullable'
            ],
            'freedom_fighter_status' => [
                'int',
                'required',
                Rule::in(CourseEnrollment::FREEDOM_FIGHTER_STATUSES)
            ],
            'passport_photo_path' => [
                'string',
                'nullable',
            ],
            'signature_image_path' => [
                'string',
                'nullable',
            ],

            'present_address' => [
                'array',
                'required'
            ],
            'present_address.*.loc_division_id' => [
                'required',
                'integer',
            ],
            'present_address.*.loc_district_id' => [
                'required',
                'integer',
            ],
            'present_address.*.loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'present_address.*.village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'present_address.*.village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'present_address.*.house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'present_address.*.house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'present_address.*.zip_or_postal_code' => [
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],

            'is_permanent_address' => [
                'int',
                'required'
            ],

            'permanent_address' => [
                Rule::requiredIf(function () use ($request) {
                    return $request['is_permanent_address'] == BaseModel::TRUE;
                }),
                'array'
            ],
            'permanent_address.*.loc_division_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request['is_permanent_address'] == BaseModel::TRUE;
                }),
                'integer',
            ],
            'permanent_address.*.loc_district_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request['is_permanent_address'] == BaseModel::TRUE;
                }),
                'integer',
            ],
            'permanent_address.*.loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'permanent_address.*.village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'permanent_address.*.village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'permanent_address.*.house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'permanent_address.*.house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'permanent_address.*.zip_or_postal_code' => [
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],

            'main_profession' => [
                'required',
                'string',
                'max:500'
            ],
            'main_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'other_profession' => [
                'nullable',
                'string',
                'max:500'
            ],
            'other_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'monthly_income' => [
                'required',
                'numeric'
            ],
            'is_currently_employed' => [
                'nullable',
                'int'
            ],
            'years_of_experiences' => [
                'nullable',
                'int'
            ],
            'passing_year' => [
                'nullable',
                'string'
            ],
            'father_name' => [
                'required',
                'string',
                'max:500'
            ],
            'father_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'father_nid' => [
                'nullable',
                'string',
                'max:30'
            ],
            'father_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'father_date_of_birth' => [
                'nullable',
                'date'
            ],
            'mother_name' => [
                'required',
                'string',
                'max:500'
            ],
            'mother_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'mother_nid' => [
                'nullable',
                'string',
                'max:30'
            ],
            'mother_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'mother_date_of_birth' => [
                'nullable',
                'date'
            ],
            'has_own_family_home' => [
                'nullable',
                'int'
            ],
            'has_own_family_land' => [
                'nullable',
                'int'
            ],
            'number_of_siblings' => [
                'nullable',
                'int'
            ],
            'recommended_by_any_organization' => [
                'nullable',
                'int'
            ],
            'education_info' => [
                'nullable',
                'array',
            ],
        ];
        if (isset($request['education_info']) && is_array($request['education_info'])) {
            foreach ($request['education_info'] as $eduLabelId => $fields) {
                $validationField = 'education_info.' . $eduLabelId . '.';
                $rules[$validationField . 'exam_degree_id'] = [
                    'required',
                    'int'
                ];
                $rules[$validationField . 'exam_degree_name'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $request) {
                        return $this->getRequiredStatus(EnrollmentEducation::EXAM_DEGREE_NAME, $eduLabelId);
                    }),
                    "string"
                ];
                $rules[$validationField . 'exam_degree_name_en'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'major_or_concentration'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $request) {
                        return $this->getRequiredStatus(EnrollmentEducation::MAJOR, $eduLabelId);
                    }),
                    "string"
                ];
                $rules[$validationField . 'major_or_concentration_en'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'edu_group_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $request) {
                        return $this->getRequiredStatus(EnrollmentEducation::EDU_GROUP, $eduLabelId);
                    }),
                    'exists:edu_groups,id,deleted_at,NULL',
                    "integer"
                ];
                $rules[$validationField . 'edu_board_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $request) {
                        return $this->getRequiredStatus(EnrollmentEducation::BOARD, $eduLabelId);
                    }),
                    'exists:edu_boards,id,deleted_at,NULL',
                    "integer"
                ];
                $rules[$validationField . 'institute_name'] = [
                    'required',
                    'string',
                    'max:800',
                ];
                $rules[$validationField . 'institute_name_en'] = [
                    'nullable',
                    'string',
                    'max:400',
                ];
                $rules[$validationField . 'is_foreign_institute'] = [
                    'required',
                    'integer',
                    Rule::in([BaseModel::TRUE, BaseModel::FALSE])
                ];
                $rules[$validationField . 'foreign_institute_country_id'] = [
                    Rule::requiredIf(function () use ($fields, $request) {
                        return BaseModel::TRUE == isset($fields['is_foreign_institute']) ? $fields['is_foreign_institute'] : BaseModel::FALSE;
                    }),
                    "integer"
                ];
                $rules[$validationField . 'result'] = [
                    "required",
                    "integer",
                    Rule::in(array_keys(config("nise3.exam_degree_results")))
                ];
                $rules[$validationField . 'marks_in_percentage'] = [
                    Rule::requiredIf(function () use ($fields, $request) {
                        $resultId = isset($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::MARKS, $resultId) : false;
                    }),
                    "numeric"
                ];
                $rules[$validationField . 'cgpa_scale'] = [
                    Rule::requiredIf(function () use ($fields,$request) {
                        $resultId = isset($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::SCALE, $resultId) : false;
                    }),
                    Rule::in([EnrollmentEducation::GPA_OUT_OF_FOUR, EnrollmentEducation::GPA_OUT_OF_FIVE]),
                    "integer"
                ];
                $rules[$validationField . 'cgpa'] = [
                    Rule::requiredIf(function () use ($fields,$request) {
                        $resultId = isset($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::CGPA, $resultId) : false;
                    }),
                    'numeric'
                ];
                $rules[$validationField . 'year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields,$request) {
                        $resultId = isset($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::YEAR_OF_PASS, $resultId) : false;
                    }),
                    'string'
                ];
                $rules[$validationField . 'expected_year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields,$request) {
                        $resultId = isset($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::EXPECTED_YEAR_OF_PASS, $resultId) : false;
                    }),
                    'string'
                ];
                $rules[$validationField . 'duration'] = [
                    "nullable",
                    "integer"
                ];
                $rules[$validationField . 'achievements'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'achievements_en'] = [
                    "nullable",
                    "string"
                ];
            }
        }


//        if($request['physical_disability_status'] == BaseModel::TRUE){
//            $rules['physical_disabilities'] = [
//                Rule::requiredIf(function () use ($id, $request) {
//                    return $request['physical_disability_status'] == BaseModel::TRUE;
//                }),
//                "array",
//                "min:1"
//            ];
//            $rules['physical_disabilities.*'] = [
//                Rule::requiredIf(function () use ($id, $request) {
//                    return $request['physical_disability_status'] == BaseModel::TRUE;
//                }),
//                "exists:physical_disabilities,id,deleted_at,NULL",
//                "int",
//                "distinct",
//                "min:1"
//            ];
//        }

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    /**
     * @param string $key
     * @param int $id
     * @return bool
     */
    private function getRequiredStatus(string $key, int $eduLabelId): bool
    {
        switch ($key) {

            /** Validation Rule Based On YouthEducation Level */
            case EnrollmentEducation::DEGREE:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_PSC_5_PASS, EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS, EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY, EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS]);
            }
            case EnrollmentEducation::BOARD:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_PSC_5_PASS, EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS, EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY]);
            }
            case EnrollmentEducation::MAJOR:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS]);
            }
            case EnrollmentEducation::EXAM_DEGREE_NAME:
            {
                return $this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId) == EducationLevel::EDUCATION_LEVEL_PHD;
            }
            case EnrollmentEducation::EDU_GROUP:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY]);
            }
            /** Validation Rule Based On Result Type */
            case EnrollmentEducation::MARKS:
            {
                return in_array($this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId), [EducationLevel::RESULT_FIRST_DIVISION, EducationLevel::RESULT_SECOND_DIVISION, EducationLevel::RESULT_THIRD_DIVISION]);
            }
            case EnrollmentEducation::SCALE:
            case EnrollmentEducation::CGPA:
            {
                return $this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId) == EducationLevel::RESULT_GRADE;
            }
            case EnrollmentEducation::YEAR_OF_PASS:
            {
                return in_array($this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId), [EducationLevel::RESULT_GRADE, EducationLevel::RESULT_ENROLLED, EducationLevel::RESULT_AWARDED, EducationLevel::RESULT_PASS]);
            }
            case EnrollmentEducation::EXPECTED_YEAR_OF_PASS:
            {
                return $this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId) == EducationLevel::RESULT_APPEARED;
            }
            default:
            {
                return false;
            }

        }
    }

    /**
     * @param string $modelName
     * @param int $id
     * @return string
     */
    public function getCodeById(string $modelName, int $id): string
    {
        if ($modelName == EnrollmentEducation::EDUCATION_LEVEL_TRIGGER) {
            $educationLevel = EducationLevel::where('id', $id)->first();
            $code = $educationLevel->code ?? "";
        } else {
            $code = config("nise3.exam_degree_results." . $id . ".code");
        }
        return $code ?? "";
    }
}
