<?php

namespace App\Services\Payment;

use App\Facade\ServiceToServiceCall;
use App\Models\RplAssessment;
use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionHistory;
use App\Models\RegisteredTrainingOrganization;
use App\Models\RplApplication;
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;


class RplApplicationCertificationPaymentService
{
    /**
     * @throws Throwable
     */
    public function paymentProcessingViaEkPay(array $request)
    {
        /** @var RplApplication $youthAssessment */
        $youthAssessment = RplApplication::findOrFail($request['youth_assessment_id']);
        $youth = ServiceToServiceCall::getYouthProfilesByIds([$youthAssessment->youth_id]);

        throw_if(empty($youth[0]), new \Exception("Youth Information is empty"));
        $youth = $youth[0];

        $rto = RegisteredTrainingOrganization::findOrFail($youthAssessment->rto_id);
        $assessment = RplAssessment::findOrFail($youthAssessment->assessment_id);


        /** ASS+RTO+I=36 is an invoice id */
        $invoicePrefix = RegisteredTrainingOrganization::YOUTH_ASSESSMENT_CERTIFICATION_INVOICE_PREFIX . $rto->code;
        $ekPayMerchantIdSize = RegisteredTrainingOrganization::YOUTH_ASSESSMENT_CERTIFICATION_INVOICE_SIZE;
        $invoiceId = CodeGeneratorService::getNewInvoiceCode($invoicePrefix, $ekPayMerchantIdSize); // Invoice id as a trnx_id;

        $time = Carbon::now()->format('Y-m-d H:i:s');

        /** Transaction Information  */
        $totalAmount = $assessment->assessment_fee;
        $currency = config('ekpay.trnx_currency');
        $paymentPurpose = PaymentTransactionHistory::YOUTH_ASSESSMENT_CERTIFICATION_FREE;
        $paymentPurposeRelatedId = $youthAssessment->id;

        $transactionId = $invoiceId;
        $orderId = $invoiceId;
        $customerId = $invoiceId;
        $orderDetail = "Youth assessment certification fee";

        /** Customer Information */
        $firstName = $youth['first_name_en'] ?? "";
        $lastName = $youth['last_name_en'] ?? "";

        $customerFullName = empty($firstName) && empty($lastName) ? "Not Available" : $firstName . " " . $lastName;
        $customerCleanName = preg_replace('/[^A-Za-z0-9 \-\.]/', '', $customerFullName);
        $customerEmail = $youth['email'];
        $customerMobileNumber = $youth['mobile'];
        $customerAddress = "";

        $ipnUri = config('ekpay.is_sand_box') ? config('ekpay.sand_box.' . $paymentPurpose . '.ipn') : config('ekpay.production.' . $paymentPurpose . '.ipn');

        $ekPayPayload = [
            "invoice" => $invoiceId,
            "payment_purpose" => PaymentTransactionHistory::PAYMENT_PURPOSE_COURSE_ENROLLMENT,
            "payment_purpose_related_id" => $paymentPurposeRelatedId,
            'mer_info' => [
                'mer_reg_id' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.' . $paymentPurpose . '.mer_info.mer_reg_id') : config('ekpay.production.' . $paymentPurpose . '.mer_info.mer_reg_id'),
                'mer_pas_key' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.' . $paymentPurpose . '.mer_info.mer_pas_key') : config('ekpay.production.' . $paymentPurpose . '.mer_info.mer_pas_key'),
            ],
            'feed_uri' => [
                's_uri' => $request['feed_uri']['success'],
                'f_uri' => $request['feed_uri']['failed'],
                'c_uri' => $request['feed_uri']['cancel'],
            ],
            'req_timestamp' => $time . ' GMT+6',
            'cust_info' => [
                'cust_id' => $customerId,
                'cust_name' => $customerCleanName,
                'cust_mobo_no' => $customerMobileNumber,
                'cust_email' => $customerEmail,
                'cust_mail_addr' => $customerAddress
            ],
            'trns_info' => [
                'trnx_id' => $transactionId,
                'trnx_amt' => $totalAmount,
                'trnx_currency' => $currency,
                'ord_id' => $orderId,
                'ord_det' => $orderDetail,
            ],
            'ipn_info' => [
                'ipn_channel' => 1,
                'ipn_email' => 'noreply@nise.gov.bd',
                'ipn_uri' => $ipnUri,
            ],
            'mac_addr' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.' . $paymentPurpose . '.mac_addr') : config('ekpay.production.' . $paymentPurpose . '.mac_addr'),
        ];

        return app(PaymentService::class)->paymentProcessing($ekPayPayload, PaymentTransactionHistory::PAYMENT_GATEWAY_EK_PAY);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function paymentValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "payment_gateway_type" => [
                "required",
                Rule::in(array_values(PaymentTransactionHistory::PAYMENT_GATEWAYS))
            ],
            "youth_assessment_id" => [
                "required",
                "integer",
                'exists:rpl_applications,id,deleted_at,NULL'
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
     * @param array $data
     * @return void
     * @throws Throwable
     */
    public function confirmationMailAndSmsSend(array $data)
    {
        if (!empty($data)) {
            /** Mail send*/
            $to = array($data['customer_email']);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "Course Enrollment Information";
            $message = "Congratulation, Your are successfully completed your payment";
            $messageBody = MailService::templateView($message);
            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();

            /** Sms send */
            $recipient = $data['customer_mobile'];
            $smsMessage = "Congratulation, Your are successfully completed your payment";
            $smsService = new SmsService();
            $smsService->sendSms($recipient, $smsMessage);
        }

    }
}
