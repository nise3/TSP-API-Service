<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use App\Models\EducationLevel;
use App\Models\EnrollmentAddress;
use App\Models\EnrollmentEducation;
use App\Models\EnrollmentGuardian;
use App\Models\EnrollmentMiscellaneous;
use App\Models\EnrollmentProfessionalInfo;
use App\Models\PhysicalDisability;
use App\Models\Youth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseEnrollmentService
{

    public function enrollCourse(array $data): CourseEnrollment
    {
        $courseEnrollment = app(CourseEnrollment::class);
        $courseEnrollment->fill($data);
        $courseEnrollment->save();

        return $courseEnrollment;
    }

    public function storeEnrollmentAddresses(array $data, CourseEnrollment $courseEnrollment): CourseEnrollment
    {
        if (!empty($data['address_info']['present_address'])) {

            $presentAddress = app(EnrollmentAddress::class);

            $addressValues = $data['address_info']['present_address'];
            $addressValues['course_enrollment_id'] = $courseEnrollment->id;
            $addressValues['address_type'] = EnrollmentAddress::ADDRESS_TYPE_PRESENT;

            $presentAddress->fill($addressValues);
            $presentAddress->save();
        }
        if ($data['address_info']['is_permanent_address'] == BaseModel::TRUE) {
            $permanentAddress = app(EnrollmentAddress::class);

            $addressValues = $data['address_info']['permanent_address'];

            $addressValues['course_enrollment_id'] = $courseEnrollment->id;
            $addressValues['address_type'] = EnrollmentAddress::ADDRESS_TYPE_PERMANENT;

            $permanentAddress->fill($addressValues);
            $permanentAddress->save();
        }

        return $courseEnrollment;
    }

    public function storeEnrollmentEducations(array $data, CourseEnrollment $courseEnrollment): CourseEnrollment
    {
        if (!empty($data['education_info'])) {
            foreach ($data['education_info'] as $eduLabelId => $values) {
                $education = app(EnrollmentEducation::class);

                $values['course_enrollment_id'] = $courseEnrollment->id;
                $values['education_level_id'] = $eduLabelId;
                $education->fill($values);
                $education->save();
            }
        }

        return $courseEnrollment;
    }

    public function storeEnrollmentProfessionalInfo(array $data, CourseEnrollment $courseEnrollment): CourseEnrollment
    {
        if (!empty($data['professional_info'])) {
            $professionalInfo = app(EnrollmentProfessionalInfo::class);
            $data['professional_info']['course_enrollment_id'] = $courseEnrollment->id;
            $professionalInfo->fill($data['professional_info']);
            $professionalInfo->save();
        }

        return $courseEnrollment;
    }

    public function storeEnrollmentGuardianInfo(array $data, CourseEnrollment $courseEnrollment): CourseEnrollment
    {
        if (!empty($data['guardian_info'])) {
            $guardianInfo = app(EnrollmentGuardian::class);
            $data['guardian_info']['course_enrollment_id'] = $courseEnrollment->id;
            $guardianInfo->fill($data['guardian_info']);
            $guardianInfo->save();
        }

        return $courseEnrollment;
    }

    public function storeEnrollmentMiscellaneousInfo(array $data, CourseEnrollment $courseEnrollment): CourseEnrollment
    {
        if (!empty($data['miscellaneous_info'])) {
            $guardianInfo = app(EnrollmentMiscellaneous::class);
            $data['miscellaneous_info']['course_enrollment_id'] = $courseEnrollment->id;
            $guardianInfo->fill($data['miscellaneous_info']);
            $guardianInfo->save();
        }

        return $courseEnrollment;
    }

    public function storeEnrollmentPhysicalDisabilities(array $data, CourseEnrollment $courseEnrollment): CourseEnrollment
    {
        if ($data['physical_disability_status'] == BaseModel::FALSE) {
            $this->detachPhysicalDisabilities($courseEnrollment);
        } else if ($data['physical_disability_status'] == BaseModel::TRUE) {
            $this->assignPhysicalDisabilities($courseEnrollment, $data['physical_disabilities']);
        }
        return $courseEnrollment;
    }

    /**
     * @param CourseEnrollment $courseEnrollment
     * @param array $disabilities
     */
    private function assignPhysicalDisabilities(CourseEnrollment $courseEnrollment, array $disabilities)
    {
        /** Assign skills to Youth */
        $disabilityIds = PhysicalDisability::whereIn("id", $disabilities)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $courseEnrollment->physicalDisabilities()->sync($disabilityIds);

    }

    /**
     * @param CourseEnrollment $courseEnrollment
     */
    private function detachPhysicalDisabilities(CourseEnrollment $courseEnrollment)
    {
        $courseEnrollment->physicalDisabilities()->sync([]);

    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function courseEnrollmentValidator(Request $request, int $id = null): Validator
    {
        $request->offsetSet('deleted_at', null);
        $data = $request->all();

        if (!empty($data["physical_disabilities"])) {
            $data["physical_disabilities"] = isset($data['physical_disabilities']) && is_array($data['physical_disabilities']) ? $data['physical_disabilities'] : explode(',', $data['physical_disabilities']);
        }

        $customMessage = [
            "course_id.unique_with" => "Course Already Enrolled"
        ];

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
                'min:1',
                'unique_with:course_enrollments,youth_id,deleted_at',
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
            "physical_disability_status" => [
                "required",
                "int",
                Rule::in([BaseModel::TRUE,BaseModel::FALSE])
            ],
            'address_info' => [
                'nullable',
                'array',
                'min:1'
            ],
            'address_info.present_address' => [
                Rule::requiredIf(!empty($data['address_info'])),
                'nullable',
                'array',
            ],
            'address_info.present_address.loc_division_id' => [
                Rule::requiredIf(!empty($data['address_info']['present_address'])),
                'nullable',
                'integer',
            ],
            'address_info.present_address.loc_district_id' => [
                Rule::requiredIf(!empty($data['address_info']['present_address'])),
                'nullable',
                'integer',
            ],
            'address_info.present_address.loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'address_info.present_address.village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'address_info.present_address.village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'address_info.present_address.house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'address_info.present_address.house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'address_info.present_address.zip_or_postal_code' => [
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],

            'address_info.is_permanent_address' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']);
                }),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],

            'address_info.permanent_address' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE;
                }),
                'nullable',
                'array',
                'min:1'
            ],
            'address_info.permanent_address.loc_division_id' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE;
                }),
                'nullable',
                'integer',
            ],
            'address_info.permanent_address.loc_district_id' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE && !empty($data['address_info']['permanent_address']);
                }),
                'nullable',
                'integer',
            ],
            'address_info.permanent_address.loc_upazila_id' => [
                'nullable',
                'integer',
            ],
            'address_info.permanent_address.village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'address_info.permanent_address.village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'address_info.permanent_address.house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'address_info.permanent_address.house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'address_info.permanent_address.zip_or_postal_code' => [
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],
            "professional_info" => [
                'nullable',
                'array',
                'min:1'
            ],
            'professional_info.main_profession' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'string',
                'max:500'
            ],
            'professional_info.main_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'professional_info.other_profession' => [
                'nullable',
                'string',
                'max:500'
            ],
            'professional_info.other_profession_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'professional_info.monthly_income' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'numeric'
            ],
            'professional_info.is_currently_employed' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::FALSE, BaseModel::TRUE])
            ],
            'professional_info.years_of_experiences' => [
                Rule::requiredIf(!empty($data['professional_info'])),
                'nullable',
                'int'
            ],
            "guardian_info" => [
                'nullable',
                'array',
                'min:1'
            ],
            'guardian_info.father_name' => [
                Rule::requiredIf(!empty($data['guardian_info'])),
                'nullable',
                'string',
                'max:500'
            ],
            'guardian_info.father_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'guardian_info.father_nid' => [
                'nullable',
                'string',
                'max:30'
            ],
            'guardian_info.father_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'guardian_info.father_date_of_birth' => [
                'nullable',
                'date'
            ],
            'guardian_info.mother_name' => [
                Rule::requiredIf(!empty($data['guardian_info'])),
                'nullable',
                'string',
                'max:500'
            ],
            'guardian_info.mother_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'guardian_info.mother_nid' => [
                'nullable',
                'string',
                'max:30'
            ],
            'guardian_info.mother_mobile' => [
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX
            ],
            'guardian_info.mother_date_of_birth' => [
                'nullable',
                'date'
            ],
            "miscellaneous_info" => [
                'nullable',
                'array',
                'min:1'
            ],
            'miscellaneous_info.has_own_family_home' => [
                Rule::requiredIf(!empty($data['miscellaneous_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'miscellaneous_info.has_own_family_land' => [
                Rule::requiredIf(!empty($data['miscellaneous_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'miscellaneous_info.number_of_siblings' => [
                'nullable',
                'int',
            ],
            'miscellaneous_info.recommended_by_any_organization' => [
                Rule::requiredIf(!empty($data['miscellaneous_info'])),
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'education_info' => [
                'nullable',
                'array',
            ],
        ];
        if (!empty($data['education_info'])) {
            foreach ($data['education_info'] as $eduLabelId => $fields) {
                $validationField = 'education_info.' . $eduLabelId . '.';
                $rules[$validationField . 'exam_degree_id'] = [
                    'required',
                    'int'
                ];
                $rules[$validationField . 'exam_degree_name'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::EXAM_DEGREE_NAME, $eduLabelId);
                    }),
                    'nullable',
                    "string"
                ];
                $rules[$validationField . 'exam_degree_name_en'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'major_or_concentration'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::MAJOR, $eduLabelId);
                    }),
                    'nullable',
                    "string"
                ];
                $rules[$validationField . 'major_or_concentration_en'] = [
                    "nullable",
                    "string"
                ];
                $rules[$validationField . 'edu_group_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::EDU_GROUP, $eduLabelId);
                    }),
                    'nullable',
                    'exists:edu_groups,id,deleted_at,NULL',
                    "integer"
                ];
                $rules[$validationField . 'edu_board_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::BOARD, $eduLabelId);
                    }),
                    'nullable',
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
                    Rule::requiredIf(function () use ($fields, $data) {
                        return BaseModel::TRUE == !empty($fields['is_foreign_institute']) ? $fields['is_foreign_institute'] : BaseModel::FALSE;
                    }),
                    'nullable',
                    "integer"
                ];
                $rules[$validationField . 'result'] = [
                    "required",
                    "integer",
                    Rule::in(array_keys(config("nise3.exam_degree_results")))
                ];
                $rules[$validationField . 'marks_in_percentage'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::MARKS, $resultId) : false;
                    }),
                    'nullable',
                    "numeric"
                ];
                $rules[$validationField . 'cgpa_scale'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::SCALE, $resultId) : false;
                    }),
                    'nullable',
                    Rule::in([EnrollmentEducation::GPA_OUT_OF_FOUR, EnrollmentEducation::GPA_OUT_OF_FIVE]),
                    "integer"
                ];
                $rules[$validationField . 'cgpa'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::CGPA, $resultId) : false;
                    }),
                    'nullable',
                    'numeric'
                ];
                $rules[$validationField . 'year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::YEAR_OF_PASS, $resultId) : false;
                    }),
                    'nullable',
                    'string'
                ];
                $rules[$validationField . 'expected_year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId ? $this->getRequiredStatus(EnrollmentEducation::EXPECTED_YEAR_OF_PASS, $resultId) : false;
                    }),
                    'nullable',
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

        if (isset($request['physical_disability_status']) && $request['physical_disability_status'] == BaseModel::TRUE) {
            $rules['physical_disabilities'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                'nullable',
                "array",
                "min:1"
            ];
            $rules['physical_disabilities.*'] = [
                Rule::requiredIf(function () use ($data) {
                    return $data['physical_disability_status'] == BaseModel::TRUE;
                }),
                'nullable',
                "int",
                "distinct",
                "min:1",
                "exists:physical_disabilities,id,deleted_at,NULL",
            ];
        }

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
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
