<?php

namespace App\Services\Payment;


use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionHistory;
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class CourseEnrollmentPaymentService
{

    /**
     * @throws Throwable
     */
    public function enrollmentEkPayPaymentProcessing(array $request)
    {
        /** @var CourseEnrollment $courseEnrollment */
        $courseEnrollment = CourseEnrollment::findOrFail($request['course_enrollment_id']);

        Log::channel('ek_pay')->info("Course enrollment Info for id-" . $courseEnrollment->id . json_encode($courseEnrollment));

        /** @var Course $courseInfo */
        $courseInfo = Course::findOrFail($courseEnrollment->course_id);

        Log::channel('ek_pay')->info("Course Info for course_id-" . $courseEnrollment->course_id . json_encode($courseInfo));

        $baseUrl = BaseModel::INSTITUTE_REMOTE_BASE_URL;
        if (request()->getHost() == 'localhost' || request()->getHost() == '127.0. 0.1') {
            $baseUrl = BaseModel::INSTITUTE_LOCAL_BASE_URL;
        }

        $ipnUri = $baseUrl . "api/v1/course-enrollment/payment-by-ek-pay/ipn-handler/" . Uuid::uuid();

        /** EN+CourseCode+I=36 is an invoice id */
        $invoicePrefix = CourseEnrollment::INVOICE_PREFIX . $courseInfo->code;
        $ekPayMerchantIdSize = CourseEnrollment::MERCHANT_ID_SIZE;
        $invoiceId = CodeGeneratorService::getNewInvoiceCode($invoicePrefix, $ekPayMerchantIdSize); // Invoice id as a trnx_id;

        $paymentGatewayPayLoad = [
            "invoice" => $invoiceId,
            "payment_purpose_code" => PaymentTransactionHistory::PAYMENT_PURPOSE_COURSE_ENROLLMENT_CODE,
            "payment_purpose_related_id" => $courseEnrollment->id,
            "customer" => [
                "id" => $courseEnrollment->youth_id,
                "name" => $courseEnrollment->first_name . " " . $courseEnrollment->last_name,
                "email" => $courseEnrollment->email,
                "mobile" => $courseEnrollment->mobile,
            ],
            'payment' => [
                'trnx_id' => $invoiceId,
                'trnx_amt' => $courseInfo->course_fee,
                'trnx_currency' => config('ekpay.trnx_currency'),
                'ord_id' => $invoiceId,
                'ord_det' => 'Course Enrollment Fee',
            ],
            "feed_uri" => $request['feed_uri'],
            "ipn_info" => [
                "ipn_uri" => $ipnUri
            ]
        ];

        return app(PaymentService::class)->paymentProcessing($paymentGatewayPayLoad, PaymentTransactionHistory::PAYMENT_GATEWAY_EK_PAY);
    }

    public function isNotSMSVerified(array $data): bool
    {
        return (bool)CourseEnrollment::where('id', $data['course_enrollment_id'])->whereNull('verification_code_verified_at')->count();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function ekPayPaymentValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "payment_gateway_type" => [
                "required",
                Rule::in(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS))
            ],
            "course_enrollment_id" => [
                "required",
                "integer",
                'exists:course_enrollments,id,deleted_at,NULL'
            ],
            "feed_uri.success" => [
                "required",
                "url"
            ],
            "feed_uri.failed" => [
                "required",
                "url"
            ],
            "feed_uri.cancel" => [
                "required",
                "url"
            ]
        ];
        return Validator::make($request->all(), $rules);
    }


    /**
     * @param CourseEnrollment $courseEnroll
     * @return void
     * @throws Throwable
     */
    public function confirmationMailAndSmsSend(CourseEnrollment $courseEnroll)
    {
        /** Mail send*/
        $to = array($courseEnroll->email);
        $from = BaseModel::NISE3_FROM_EMAIL;
        $subject = "Course Enrollment Information";
        $message = "Congratulation, You are successfully enrolled in " . $courseEnroll->course->title . ". You are assigned in any batch later.";
        $messageBody = MailService::templateView($message);
        $mailService = new MailService($to, $from, $subject, $messageBody);
        $mailService->sendMail();

        /** Sms send */
        $recipient = $courseEnroll->mobile;
        $smsMessage = "You are successfully enrolled in " . $courseEnroll->course->title;
        $smsService = new SmsService();
        $smsService->sendSms($recipient, $smsMessage);

    }
}
