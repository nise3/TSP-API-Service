<?php

namespace App\Services\Payment;


use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionHistory;
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Carbon\Carbon;
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

        $ipnUri = env('API_GATEWAY_BASE_URL', 'https://gateway-dev.nise3.xyz') . "/" . env('PAYMENT_GATEWAY_IPN_ENDPOINT_BASE_URI', 'payment-gateway-ipn-endpoint') . "/" . env('EK_PAY_IPN_URI', 'course-enrollment/payment-by-ek-pay/ipn-handler') . "/" . Uuid::uuid();

        /** EN+CourseCode+I=36 is an invoice id */
        $invoicePrefix = CourseEnrollment::INVOICE_PREFIX . $courseInfo->code;
        $ekPayMerchantIdSize = CourseEnrollment::MERCHANT_ID_SIZE;
        $invoiceId = CodeGeneratorService::getNewInvoiceCode($invoicePrefix, $ekPayMerchantIdSize); // Invoice id as a trnx_id;

        $time = Carbon::now()->format('Y-m-d H:i:s');
        $customerFullName = $courseEnrollment->first_name . " " . $courseEnrollment->last_name;
        $customerCleanName = preg_replace('/[^A-Za-z0-9 \-\.]/', '', $customerFullName);
        $paymentPurpose = PaymentTransactionHistory::PAYMENT_PURPOSE_COURSE_ENROLLMENT;

        $ekPayPayload = [
            "invoice" => $invoiceId,
            "payment_purpose" => PaymentTransactionHistory::PAYMENT_PURPOSE_COURSE_ENROLLMENT,
            "payment_purpose_related_id" => $courseEnrollment->id,
            'mer_info' => [
                'mer_reg_id' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.mer_info.mer_reg_id') : config('ekpay.production.' . $paymentPurpose . '.mer_info.mer_reg_id'),
                'mer_pas_key' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.mer_info.mer_pas_key') : config('ekpay.production.' . $paymentPurpose . '.mer_info.mer_pas_key'),
            ],
            'feed_uri' => [
                's_uri' => $request['feed_uri']['success'],
                'f_uri' => $request['feed_uri']['failed'],
                'c_uri' => $request['feed_uri']['cancel'],
            ],
            'req_timestamp' => $time . ' GMT+6',
            'cust_info' => [
                'cust_id' => $invoiceId,
                'cust_name' => $customerCleanName,
                'cust_mobo_no' => $courseEnrollment->mobile,
                'cust_email' => $courseEnrollment->email,
                'cust_mail_addr' => $courseEnrollment->address ?? " "
            ],
            'trns_info' => [
                'trnx_id' => $invoiceId,
                'trnx_amt' => $courseInfo->course_fee,
                'trnx_currency' => config('ekpay.trnx_currency'),
                'ord_id' => $invoiceId,
                'ord_det' => 'Course Enrollment Fee',
            ],
            'ipn_info' => [
                'ipn_channel' => 1,
                'ipn_email' => 'noreply@nise.gov.bd',
                'ipn_uri' => $ipnUri,
            ],
            'mac_addr' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.mac_addr') : config('ekpay.production.' . $paymentPurpose . '.mac_addr'),
        ];

        return app(PaymentService::class)->paymentProcessing($ekPayPayload, PaymentTransactionHistory::PAYMENT_GATEWAY_EK_PAY);
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
        if (!empty($courseEnroll)) {
            /** Mail send*/
            $to = array($courseEnroll->email);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "Course Enrollment Information";
            $message = "Congratulation, You are successfully enrolled in " . $courseEnroll->course->title . ". You will be assigned in a batch later on.";
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
}
