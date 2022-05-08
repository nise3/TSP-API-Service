<?php


namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamType;
use App\Models\PaymentTransactionHistory;
use App\Models\EducationLevel;
use App\Models\EnrollmentAddress;
use App\Models\EnrollmentEducation;
use App\Models\EnrollmentGuardian;
use App\Models\EnrollmentMiscellaneous;
use App\Models\EnrollmentProfessionalInfo;
use App\Models\PhysicalDisability;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\Payment\CourseEnrollmentPaymentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

/**
 *
 */
class CourseEnrollmentService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getCourseEnrollmentList(array $request, Carbon $startTime): array
    {
        $instituteId = $request['institute_id'] ?? "";
        $industryAssociationId = $request['industry_association_id'] ?? "";
        $firstName = $request['first_name'] ?? "";
        $firstNameEn = $request['first_name_en'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $courseId = $request['course_id'] ?? "";
        $courseTitle = $request['course_title'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";
        $paymentStatus = $request['payment_status'] ?? "";
        $programId = $request['program_id'] ?? "";
        $programTitle = $request['program_title'] ?? "";
        $batchId = $request['batch_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var CourseEnrollment|Builder $coursesEnrollmentBuilder */
        $coursesEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'course_enrollments.institute_id',
                'course_enrollments.industry_association_id',
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
                'batches.title as batch_title',
                'batches.title_en as batch_title_en',
                'batches.certificate_id as certificate_id',
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
        )->acl();

        if (is_numeric($instituteId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.institute_id', $instituteId);
        }

        if (is_numeric($industryAssociationId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.industry_association_id', $industryAssociationId);
        }

        $coursesEnrollmentBuilder->join("courses", function ($join) use ($rowStatus) {
            $join->on('course_enrollments.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
        });

        $coursesEnrollmentBuilder->leftJoin("training_centers", function ($join) {
            $join->on('course_enrollments.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $coursesEnrollmentBuilder->leftJoin("programs", function ($join) {
            $join->on('course_enrollments.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
        });

        $coursesEnrollmentBuilder->leftJoin("batches", function ($join) {
            $join->on('course_enrollments.batch_id', '=', 'batches.id')
                ->whereNull('batches.deleted_at');
        });

        $coursesEnrollmentBuilder->orderBy('course_enrollments.id', $order);

        if (is_numeric($rowStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.row_status', $rowStatus);
        }

        if (is_numeric($paymentStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.payment_status', $paymentStatus);
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

        if (!empty($courseTitle)) {
            $coursesEnrollmentBuilder->where('courses.title', 'like', '%' . $courseTitle . '%');
            $coursesEnrollmentBuilder->orWhere('courses.title_en', 'like', '%' . $courseTitle . '%');
        }

        if (is_numeric($programId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.program_id', '=', $programId);
        }

        if (!empty($programTitle)) {
            $coursesEnrollmentBuilder->where('programs.title', 'like', '%' . $programTitle . '%');
            $coursesEnrollmentBuilder->orWhere('programs.title_en', 'like', '%' . $programTitle . '%');
        }

        if (is_numeric($batchId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.batch_id', $batchId);
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
     * @return CourseEnrollment
     */
    public function getOneCourseEnrollment(int $id): CourseEnrollment
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
                'batches.title as batch_title',
                'batches.title_en as batch_title_en',
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

        $courseEnrollmentBuilder->leftJoin("batches", function ($join) {
            $join->on('course_enrollments.batch_id', '=', 'batches.id')
                ->whereNull('batches.deleted_at');
        });

        $courseEnrollmentBuilder->with('educations');
        $courseEnrollmentBuilder->with('addresses');
        $courseEnrollmentBuilder->with('guardian');
        $courseEnrollmentBuilder->with('miscellaneous');
        $courseEnrollmentBuilder->with('physicalDisabilities');

        return $courseEnrollmentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @param bool $isBulkImport
     * @return CourseEnrollment
     */
    public function enrollCourse(array $data, bool $isBulkImport = false): CourseEnrollment
    {
        $courseEnrollment = app(CourseEnrollment::class);

        $course = Course::find($data['course_id']);

        if (!empty($course->institute_id)) {
            $data['institute_id'] = $course->institute_id;
        } elseif (!empty($course->industry_association_id)) {
            $data['industry_association_id'] = $course->industry_association_id;
        }
        $data['row_status'] = $isBulkImport ? BaseModel::ROW_STATUS_ACTIVE : BaseModel::ROW_STATUS_PENDING;
        $data['saga_status'] = $isBulkImport ? BaseModel::SAGA_STATUS_COMMIT : BaseModel::SAGA_STATUS_CREATE_PENDING;

        $courseEnrollment->fill($data);
        $courseEnrollment->save();

        return $courseEnrollment;
    }

    /**
     * @param array $data
     * @param CourseEnrollment $courseEnrollment
     * @return CourseEnrollment
     */
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
        if (!empty($data['address_info']['is_permanent_address']) && $data['address_info']['is_permanent_address'] == BaseModel::TRUE) {
            $permanentAddress = app(EnrollmentAddress::class);

            $addressValues = $data['address_info']['permanent_address'];

            $addressValues['course_enrollment_id'] = $courseEnrollment->id;
            $addressValues['address_type'] = EnrollmentAddress::ADDRESS_TYPE_PERMANENT;

            $permanentAddress->fill($addressValues);
            $permanentAddress->save();
        }

        return $courseEnrollment;
    }

    /**
     * @param array $data
     * @param CourseEnrollment $courseEnrollment
     * @return CourseEnrollment
     */
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

    /**
     * @param array $data
     * @param CourseEnrollment $courseEnrollment
     * @return CourseEnrollment
     */
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

    /**
     * @param array $data
     * @param CourseEnrollment $courseEnrollment
     * @return CourseEnrollment
     */
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

    /**
     * @param array $data
     * @param CourseEnrollment $courseEnrollment
     * @return CourseEnrollment
     */
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

    /**
     * @param array $data
     * @param CourseEnrollment $courseEnrollment
     * @return CourseEnrollment
     */
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

    public function smsCodeValidation(Request $request): Validator
    {
        $rules = [
            "verification_code" => [
                "required",
                "digits:4"
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    public function rollbackYouth(array $payload)
    {
        foreach ($payload as $value) {
            ServiceToServiceCall::rollbackYouthUserById($value['mobile']);
        }

    }


    /**
     * @param CourseEnrollment $courseEnrollment
     */
    private function detachPhysicalDisabilities(CourseEnrollment $courseEnrollment)
    {
        $courseEnrollment->physicalDisabilities()->sync([]);

    }

    /**
     * @param int $id
     * @param string $code
     * @return array
     */
    public function verifySMSCode(int $id, string $code): array
    {
        /** @var CourseEnrollment $courseEnrollment */
        $courseEnrollment = CourseEnrollment::where("id", $id)
            ->where("verification_code", $code)
            ->where("row_status", BaseModel::ROW_STATUS_PENDING)
            ->first();
        $verifyStatus = false;
        $verifyMessage = 'Unprocessable Request';
        if ($courseEnrollment) {

            $verifyStatus = true;
            $verifyMessage = 'Sms Verification is already done';

            if (!$courseEnrollment->verification_code_verified_at) {
                $courseEnrollment->verification_code_verified_at = Carbon::now();
                $courseEnrollment->save();
                $verifyMessage = 'Sms Verification is done';
            }
        }
        return [
            $verifyStatus,
            $verifyMessage
        ];
    }

    public function isFreeCourse(int $id): int
    {
        /** @var CourseEnrollment $courseEnrollment */
        $courseEnrollment = CourseEnrollment::where("id", $id)
            ->where("row_status", BaseModel::ROW_STATUS_PENDING)
            ->first();

        $verificationSuccessStatus = 0;
        if ($courseEnrollment) {
            Log::channel('ek_pay')->info("Course Fee for Free Course= " . $courseEnrollment->course->course_fee);
            Log::channel('ek_pay')->info("Parsing Value Of course fee= " . doubleval($courseEnrollment->course->course_fee));
            /** Course fee zero check for free course */
            if (doubleval($courseEnrollment->course->course_fee) == 0) {
                Log::channel('ek_pay')->info("Free Course");
                $courseEnrollment->row_status = BaseModel::ROW_STATUS_ACTIVE;
                $courseEnrollment->payment_status = PaymentTransactionHistory::PAYMENT_SUCCESS;
                $courseEnrollment->save();
                $verificationSuccessStatus = 1;
                app(CourseEnrollmentPaymentService::class)->confirmationMailAndSmsSend($courseEnrollment);
            }

        }
        return $verificationSuccessStatus;
    }

    /**
     * @param CourseEnrollment $courseEnrollment
     * @param string $code
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function sendSmsVerificationCode(CourseEnrollment $courseEnrollment, string $code): bool
    {
        $mobile = $courseEnrollment->mobile;
        $email = $courseEnrollment->email;
        $message = "Your Course Enrollment Verification code : " . $code;
        if ($mobile) {
            app(SmsService::class)->sendSms($mobile, $message);
            Log::info('Sms send after enrollment to number--->' . $mobile);
        }
        if ($email) {
            $subject = "Your Course Enrollment Verification code";
            $from = BaseModel::NISE3_FROM_EMAIL;
            $messageBody = MailService::templateView($message);
            $mailService = new MailService([$email], $from, $subject, $messageBody);
            $mailService->sendMail();
        }
        return true;
    }

    /**
     * @param CourseEnrollment $courseEnrollment
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function resendCode(CourseEnrollment $courseEnrollment): bool
    {
        $code = generateOtp(4);
        $courseEnrollment->verification_code = $code;
        $courseEnrollment->verification_code_sent_at = Carbon::now();
        $courseEnrollment->save();
        return $this->sendSmsVerificationCode($courseEnrollment, $code);
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
            'institute_id' => 'nullable|int|gt:0|exists:institutes,id,deleted_at,NULL',
            'industry_association_id' => 'nullable|int|gt:0',
            'first_name' => 'nullable|max:500|min:2',
            'first_name_en' => 'nullable|max:250|min:2',
            'program_id' => 'nullable|int|gt:0',
            'program_title' => 'nullable|string|min:2',
            'payment_status' => [
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
            ],
            'course_id' => 'nullable|int|gt:0',
            'batch_id' => 'nullable|int|gt:0',
            'course_title' => 'nullable|string|min:2',
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
     * @return Validator
     */
    public function courseEnrollmentValidator(Request $request): Validator
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
            'youth_code' => [
                'required',
                'string'
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
                //'unique_with:course_enrollments,youth_id,deleted_at,
                function ($attr, $value, $failed) use ($data) {
                    $courseEnrollments = CourseEnrollment::where('youth_id', $data['youth_id'])->where('course_id', $value)->get();
                    foreach ($courseEnrollments as $courseEnrollment) {
                        if ($courseEnrollment->saga_status == BaseModel::SAGA_STATUS_CREATE_PENDING ||
                            $courseEnrollment->saga_status == BaseModel::SAGA_STATUS_UPDATE_PENDING ||
                            $courseEnrollment->saga_status == BaseModel::SAGA_STATUS_DESTROY_PENDING) {
                            $failed("You already enrolled in this course but enrollment process is in Pending status");
                        } else if ($courseEnrollment->saga_status == BaseModel::SAGA_STATUS_COMMIT) {
                            $failed("You already enrolled in this course!");
                        }
                    }
                }
            ],
            'training_center_id' => [
                'nullable',
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
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(5))) {
                        $failed('Age should be greater than 5 years.');
                    }
                }
            ],
            'email' => [
                'required',
                'email',
            ],
            "mobile" => [
                "required",
                "max:11",
                BaseModel::MOBILE_REGEX
            ],
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
                'nullable',
                'int',
                Rule::in([BaseModel::TRUE, BaseModel::FALSE])
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
                'nullable',
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
                "nullable",
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
                'date',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(25))) {
                        $failed('Age should be greater than 25 years.');
                    }
                }
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
                'date',
                function ($attr, $value, $failed) {
                    if (Carbon::parse($value)->greaterThan(Carbon::now()->subYear(25))) {
                        $failed('Age should be greater than 25 years.');
                    }
                }
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
                    Rule::requiredIf(function () use ($eduLabelId, $request) {
                        return $this->getRequiredStatus(EnrollmentEducation::DEGREE, $eduLabelId);
                    }),
                    'nullable',
                    'int',
                    'exists:exam_degrees,id,deleted_at,NULL,education_level_id,' . $eduLabelId
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
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::MARKS, $resultId);
                    }),
                    'nullable',
                    "numeric"
                ];
                $rules[$validationField . 'cgpa_scale'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::SCALE, $resultId);
                    }),
                    'nullable',
                    Rule::in([EnrollmentEducation::GPA_OUT_OF_FOUR, EnrollmentEducation::GPA_OUT_OF_FIVE]),
                    "integer"
                ];
                $rules[$validationField . 'cgpa'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::CGPA, $resultId);
                    }),
                    'nullable',
                    'numeric',
                    "max:5"
                ];
                $rules[$validationField . 'year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::YEAR_OF_PASS, $resultId);
                    }),
                    'nullable',
                    'string'
                ];
                $rules[$validationField . 'expected_year_of_passing'] = [
                    Rule::requiredIf(function () use ($fields, $data) {
                        $resultId = !empty($fields['result']) ? $fields['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::EXPECTED_YEAR_OF_PASS, $resultId);
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

        if (!empty($request['payment_info'])) {
            $rules['payment_gateway_type'] = [
                'required',
                Rule::in(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS))
            ];
        }
        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }

    /**
     * @param string $key
     * @param int $eduLabelId
     * @return bool
     */
    public function getRequiredStatus(string $key, int $eduLabelId): bool
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
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS, EducationLevel::EDUCATION_LEVEL_PHD]);
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
                return $this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId) !== EducationLevel::RESULT_APPEARED;
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
                'course_enrollments.batch_id',
                'courses.id as course_id',
                'courses.cover_image',
                'courses.code as course_code',
                'courses.level as course_level',
                'courses.language_medium as course_language_medium',
                'courses.title as course_title',
                'courses.title_en as course_title_en',
                'courses.course_fee as course_fee',
                'courses.duration as duration',
                'courses.created_at as course_created_at',
                'institutes.id as institute_id',
                'institutes.title as institute_title',
                'institutes.title_en as institute_title_en',
                'course_enrollments.row_status',
                'course_enrollments.created_at',
                'course_enrollments.updated_at'
            ]
        );


        if (is_numeric($youthId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.youth_id', $youthId);
        }
        $coursesEnrollmentBuilder->join("courses", function ($join) {
            $join->on('course_enrollments.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
        });

        $coursesEnrollmentBuilder->join("institutes", function ($join) {
            $join->on('course_enrollments.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $coursesEnrollmentBuilder->orderBy('course_enrollments.id', $order);
        if (is_numeric($rowStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.row_status', $rowStatus);
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

        $courseEnrollments = $courseEnrollments->toArray() ?? [];


        foreach ($courseEnrollments as &$courseEnrollment) {

            /** @var Builder $examsBuilder */
            $examsBuilder = ExamType::select([
                'batches.id as batch_id',
                'exam_types.title',
                'exam_types.title_en',
                'exam_types.published_at',
                'exams.type',
                'batches.id as batch_id',
                'batches.title as batch_title',
                'batches.title_en as batch_title_en',
                'exams.id as exam_id',
                'exams.exam_date',
                'exams.duration',
                'exam_subjects.title as subject_title',
                'exam_subjects.title_en as subject_title_en',
            ]);

            $examsBuilder->where('exam_types.is_published', '=', Exam::EXAM_PUBLISHED);


            $examsBuilder->join("batches", function ($join) {
                $join->on('exam_types.purpose_id', '=', 'batches.id')
                    ->whereNull('batches.deleted_at');
            });

            $examsBuilder->join("exam_subjects", function ($join) {
                $join->on('exam_types.subject_id', '=', 'exam_subjects.id')
                    ->whereNull('exam_subjects.deleted_at');
            });


            $examsBuilder->join("exams", function ($join) {
                $join->on('exam_types.id', '=', 'exams.exam_type_id')
                    ->whereNull('exams.deleted_at');
            });

              //TODO:Need To Implement This

//            SELECT * from exams left join exam_results on exams.id = exam_results.exam_id group by exam_results.id
//             DB::raw("IF(COUNT(exam_results.id) > 0, 'true', 'false') AS participated")

            $examsBuilder->where('exam_types.purpose_id', '=', $courseEnrollment['batch_id']);


            $examsBuilder = $examsBuilder->get();
            $exams = $examsBuilder->toArray() ?? [];

            //TODO:Remove Loop And Implement Query

            foreach ($exams as &$exam) {
                $examParticipation = ExamResult::where('exam_results.exam_id', $exam['exam_id'])
                    ->where('exam_results.youth_id', '=', $courseEnrollment['youth_id'])->count('exam_results.id');
                if ($examParticipation > 0) {
                    $exam['participated'] = true;
                } else {
                    $exam['participated'] = false;
                }
            }


            $courseEnrollment['exams'] = $exams;
        }

        $courseEnrollments = $courseEnrollments['data'] ?? $courseEnrollments;
        $response['order'] = $order;
        $response['data'] = $courseEnrollments;
        $response['_response_status'] = [
            "success" => true,
            "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param array $request
     * @return array
     */
    public function getEnrolledYouths(array $request): array
    {
        $courseId = $request['course_id'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var CourseEnrollment|Builder $coursesEnrollmentBuilder */
        $coursesEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'course_enrollments.course_id'
            ]
        );

        if (!empty($courseId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.course_id', '=', $courseId);
        }

        if (is_numeric($rowStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.row_status', $rowStatus);
        }

        $coursesEnrollmentBuilder->orderBy('course_enrollments.id', $order);

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

        /** Add youth details */
        $youthIds = $courseEnrollments->pluck('youth_id')->unique()->toArray();
        if ($youthIds) {
            $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
            $indexedYouths = [];

            foreach ($youthProfiles as $item) {
                $indexedYouths[$item['id']] = $item;
            }

            foreach ($courseEnrollments as $enrollment){
                $enrollment['youth_details'] = $indexedYouths[$enrollment->youth_id];
            }
        }

        $response['order'] = $order;
        $response['data'] = $courseEnrollments->toArray()['data'] ?? $courseEnrollments->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK
        ];

        return $response;
    }

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getInstituteTraineeYouths(array $request, Carbon $startTime): array
    {
        $instituteId = $request['institute_id'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var CourseEnrollment|Builder $coursesEnrollmentBuilder */
        $coursesEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'course_enrollments.institute_id',
                'institutes.title as institute_title',
                'institutes.title_en as institute_title_en',
                'course_enrollments.row_status',
                'course_enrollments.created_at',
                'course_enrollments.updated_at'
            ]
        );

        $coursesEnrollmentBuilder->whereNotNull('course_enrollments.batch_id');

        if (!empty($instituteId)) {
            $coursesEnrollmentBuilder->where('course_enrollments.institute_id', $instituteId);
        }

        $coursesEnrollmentBuilder->join("institutes", function ($join) {
            $join->on('course_enrollments.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $coursesEnrollmentBuilder->orderBy('course_enrollments.id', $order);

        if (is_numeric($rowStatus)) {
            $coursesEnrollmentBuilder->where('course_enrollments.row_status', $rowStatus);
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

        $youthIds = $courseEnrollments->pluck('youth_id')->unique()->toArray();
        if ($youthIds) {
            $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
            $indexedYouths = [];

            foreach ($youthProfiles as $item) {
                $indexedYouths[$item['id']] = $item;
            }

            foreach ($courseEnrollments as $courseEnrollment) {
                //TODO: this line should be checked. If not need then remove it
                //$courseEnrollment['youth_details'] = $indexedYouths[$courseEnrollment['youth_id']] ?? "";
                $name = $indexedYouths[$courseEnrollment['youth_id']]['first_name'] ?? "" . ' ' . $indexedYouths[$courseEnrollment['youth_id']]['last_name'] ?? "";
                $nameEn = $indexedYouths[$courseEnrollment['youth_id']]['first_name_en'] ?? "" . ' ' . $indexedYouths[$courseEnrollment['youth_id']]['last_name_en'] ?? "";
                $courseEnrollment['youth_name'] = $name;
                $courseEnrollment['youth_name_en'] = $nameEn;
            }
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

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function enrolledYouthsFilterValidator(Request $request): Validator
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

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function instituteTraineeYouthsFilterValidator(Request $request): Validator
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
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id,deleted_at,NULL'
            ],
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
     * @return Validator
     */
    public function batchAssignmentValidator(Request $request): Validator
    {
        $requestData = $request->all();

        $rules = [
            'enrollment_id' => [
                'required',
                'int',
                'min:1',
                'exists:course_enrollments,id,deleted_at,NULL'
            ],
            'batch_id' => [
                'required',
                'int',
                'min:1',
                'exists:batches,id,deleted_at,NULL',
                function ($attr, $value, $failed) {
                    $selectedBatch = Batch::findOrFail($value);
                    $numberOfSeats = $selectedBatch->number_of_seats;
                    $numberOfEnrollmentsInBatch = CourseEnrollment::where('batch_id', $value)->count();
                    if ($numberOfEnrollmentsInBatch >= $numberOfSeats) {
                        $failed("Batch maximum seats exceed");
                    }
                }
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules);
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function rejectCourseEnrollmentValidator(Request $request): Validator
    {
        $requestData = $request->all();

        $rules = [
            'enrollment_id' => 'required|min:1|exists:course_enrollments,id,deleted_at,NULL',
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules);
    }

    /**
     * @param array $data
     * @param Batch $batch
     * @return mixed
     * @throws Throwable
     */
    public function assignBatch(array $data, Batch $batch): CourseEnrollment
    {
        DB::beginTransaction();
        try {
            $courseEnrollment = CourseEnrollment::findOrFail($data['enrollment_id']);
            $courseEnrollment->batch_id = $data['batch_id'];
            $courseEnrollment->training_center_id = $batch->training_center_id;
            $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_UPDATE_PENDING;
            $courseEnrollment->row_status = BaseModel::ROW_STATUS_ACTIVE;
            $courseEnrollment->save();

            $batch = Batch::find($data['batch_id']);
            $batch['available_seats'] = $batch['available_seats'] - 1;
            $batch->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $courseEnrollment;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function rejectCourseEnrollmentApplication(array $data): mixed
    {
        $courseEnrollment = CourseEnrollment::findOrFail($data['enrollment_id']);
        $courseEnrollment->batch_id = null;
        $courseEnrollment->row_status = BaseModel::ROW_STATUS_REJECTED;

        $courseEnrollment->save();

        return $courseEnrollment;
    }

    /**
     * @param int $youthId
     * @return int
     */
    public function getEnrolledCourseCount(int $youthId): int
    {
        return CourseEnrollment::join('courses', function ($join) {
            $join->on('courses.id', 'course_enrollments.course_id')
                ->whereNull('courses.deleted_at');
        })->where('course_enrollments.youth_id', $youthId)->count('course_enrollments.id');
    }

}
