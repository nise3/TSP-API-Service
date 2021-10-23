<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\EducationLevel;
use App\Models\EnrollmentAddress;
use App\Models\EnrollmentEducation;
use App\Models\EnrollmentGuardian;
use App\Models\EnrollmentMiscellaneous;
use App\Models\EnrollmentProfessionalInfo;
use App\Models\PhysicalDisability;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CourseEnrollmentService
{

    public function getCourseEnrollmentList(array $request, Carbon $startTime): array
    {
        $instituteId = $request['institute_id'] ?? "";
        $firstName = $request['first_name'] ?? "";
        $firstNameEn = $request['first_name_en'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $courseId = $request['course_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";
        $programId = $request['program_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var CourseEnrollment|Builder $coursesEnrollmentBuilder */
        $coursesEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'course_enrollments.institute_id',
                'course_enrollments.program_id',
                'programs.title as program_title',
                'programs.title_en as program_title_en',
                'course_enrollments.course_id',
                'courses.title as course_title',
                'courses.title_en as course_title_en',
                'course_enrollments.training_center_id',
                'training_centers.title as training_center_title',
                'training_centers.title_en as training_center_title_en',
                'course_enrollments.batch_id',
                'course_enrollments.payment_status',
                'course_enrollments.first_name',
                'course_enrollments.first_name_en',
                'course_enrollments.last_name',
                'course_enrollments.last_name_en',
                'course_enrollments.gender',
                'course_enrollments.date_of_birth',
                'course_enrollments.email',
                'course_enrollments.mobile',
                'course_enrollments.identity_number_type',
                'course_enrollments.identity_number',
                'course_enrollments.religion',
                'course_enrollments.marital_status',
                'course_enrollments.nationality',
                'course_enrollments.physical_disability_status',
                'course_enrollments.freedom_fighter_status',
                'course_enrollments.row_status',
                'course_enrollments.created_at',
                'course_enrollments.updated_at'
            ]
        );

        if (is_numeric($instituteId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.institute_id', $instituteId);
        }

        $coursesEnrollmentBuilder->leftJoin("courses", function ($join) use ($rowStatus) {
            $join->on('course_enrollments.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('courses.row_status', $rowStatus);
            }
        });

        $coursesEnrollmentBuilder->leftJoin("training_centers", function ($join) use ($rowStatus) {
            $join->on('course_enrollments.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('training_centers.row_status', $rowStatus);
            }
        });

        $coursesEnrollmentBuilder->leftJoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('programs.row_status', $rowStatus);
            }
        });

        $coursesEnrollmentBuilder->orderBy('course_enrollments.id', $order);

        if (is_numeric($rowStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.row_status', $rowStatus);
        }

        if (!empty($firstName)) {
            $coursesEnrollmentBuilder->where('course_enrollments.first_name', 'like', '%' . $firstName . '%');
        }
        if (!empty($firstNameEn)) {
            $coursesEnrollmentBuilder->where('course_enrollments.first_name_en', 'like', '%' . $firstNameEn . '%');
        }

        if (is_numeric($courseId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.course_id', '=', $courseId);
        }

        if (is_numeric($programId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.program_id', '=', $programId);
        }

        if (is_numeric($trainingCenterId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.training_center_id', '=', $trainingCenterId);
        }

        /** @var Collection $courseEnrollments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $courseEnrollments = $coursesEnrollmentBuilder->paginate($pageSize);
            $paginateData = (object)$courseEnrollments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courseEnrollments = $coursesEnrollmentBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courseEnrollments->toArray()['data'] ?? $courseEnrollments->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @param bool $withTrainers
     * @return array
     */
    public function getOneCourseEnrollment(int $id, Carbon $startTime, bool $withTrainers = false): array
    {
        /** @var CourseEnrollment|Builder $courseEnrollmentBuilder */
        $courseEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'course_enrollments.institute_id',
                'course_enrollments.program_id',
                'programs.title as program_title',
                'programs.title_en as program_title_en',
                'course_enrollments.course_id',
                'courses.title as course_title',
                'courses.title_en as course_title_en',
                'course_enrollments.training_center_id',
                'training_centers.title as training_center_title',
                'training_centers.title_en as training_center_title_en',
                'course_enrollments.batch_id',
                'course_enrollments.payment_status',
                'course_enrollments.first_name',
                'course_enrollments.first_name_en',
                'course_enrollments.last_name',
                'course_enrollments.last_name_en',
                'course_enrollments.gender',
                'course_enrollments.date_of_birth',
                'course_enrollments.email',
                'course_enrollments.mobile',
                'course_enrollments.identity_number_type',
                'course_enrollments.identity_number',
                'course_enrollments.religion',
                'course_enrollments.marital_status',
                'course_enrollments.nationality',
                'course_enrollments.physical_disability_status',
                'course_enrollments.freedom_fighter_status',
                'course_enrollments.row_status',
                'course_enrollments.created_at',
                'course_enrollments.updated_at'
            ]
        );

        $courseEnrollmentBuilder->where('course_enrollments.id', '=', $id);

        $courseEnrollmentBuilder->join("courses", function ($join) {
            $join->on('course_enrollments.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at')
                ->where('courses.row_status', BaseModel::ROW_STATUS_ACTIVE);
        });

        $courseEnrollmentBuilder->leftJoin("training_centers", function ($join) {
            $join->on('course_enrollments.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at')->where('training_centers.row_status', BaseModel::ROW_STATUS_ACTIVE);
        });

        $courseEnrollmentBuilder->leftJoin("programs", function ($join) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at')->where('programs.row_status', BaseModel::ROW_STATUS_ACTIVE);
        });

        $courseEnrollmentBuilder->with('educations');
        $courseEnrollmentBuilder->with('addresses');
        $courseEnrollmentBuilder->with('guardian');
        $courseEnrollmentBuilder->with('miscellaneous');
        $courseEnrollmentBuilder->with('physicalDisabilities');

        $courseEnrollment = $courseEnrollmentBuilder->first();


        return [
            "data" => $courseEnrollment ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    public function enrollCourse(array $data): CourseEnrollment
    {
        $courseEnrollment = app(CourseEnrollment::class);
        $data['row_status'] = BaseModel::ROW_STATUS_PENDING;
        $course = Course::find($data['course_id']);
        $data['institute_id'] = $course->institute_id;
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
        if ($data['physical_disability_status'] == BaseModel::TRUE) {
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
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be between 0 to 3. [30000]'
        ];

        $requestData = $request->all();

        $rules = [
            'first_name' => 'nullable|max:500|min:2',
            'first_name_en' => 'nullable|max:250|min:2',
            'program_id' => 'nullable|int|gt:0',
            'institute_id' => 'required|int|gt:0',
            'course_id' => 'nullable|int|gt:0',
            'training_center_id' => 'nullable|int|gt:0',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in(CourseEnrollment::ROW_STATUSES),
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
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
                'nullable',
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
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
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
                    'numeric',
                    "max:5"
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

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getYouthEnrollCourses(array $request, Carbon $startTime): array
    {
        $youthId = $request['youth_id'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $courseId = $request['course_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var CourseEnrollment|Builder $coursesEnrollmentBuilder */
        $coursesEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'courses.code as course_code',
                'courses.level as course_level',
                'courses.language_medium as course_language_medium',
                'courses.title as course_title',
                'courses.title_en as course_title_en',
                'courses.course_fee as course_fee',
                'courses.duration as course_duration',
                'course_enrollments.row_status',
                'course_enrollments.created_at',
                'course_enrollments.updated_at'
            ]
        );

        if (is_numeric($youthId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.youth_id', $youthId);
        }

        $coursesEnrollmentBuilder->leftJoin("courses", function ($join) use ($rowStatus) {
            $join->on('course_enrollments.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('courses.row_status', $rowStatus);
            }
        });

        $coursesEnrollmentBuilder->orderBy('course_enrollments.id', $order);

        if (is_numeric($rowStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.row_status', $rowStatus);
        }

        if (!empty($firstName)) {
            $coursesEnrollmentBuilder->where('course_enrollments.first_name', 'like', '%' . $firstName . '%');
        }
        if (!empty($firstNameEn)) {
            $coursesEnrollmentBuilder->where('course_enrollments.first_name_en', 'like', '%' . $firstNameEn . '%');
        }

        if (is_numeric($courseId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.course_id', '=', $courseId);
        }

        /** @var Collection $courseEnrollments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $courseEnrollments = $coursesEnrollmentBuilder->paginate($pageSize);
            $paginateData = (object)$courseEnrollments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courseEnrollments = $coursesEnrollmentBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courseEnrollments->toArray()['data'] ?? $courseEnrollments->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function youthEnrollCoursesFilterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be between 0 to 3. [30000]'
        ];

        $requestData = $request->all();

        $rules = [
            'youth_id' => 'required|min:1',
            'course_id' => 'nullable|int|gt:0',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in(CourseEnrollment::ROW_STATUSES),
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }
}
