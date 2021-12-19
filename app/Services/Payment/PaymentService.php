<?php

namespace App\Services\Payment;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionLogHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Rfc4122\UuidV4;

/**
 * class PaymentService
 */
class PaymentService
{

    public function paymentProcessing(array $paymentRequestPayload)
    {
        $paymentGatewayType = $paymentRequestPayload['payment_gateway_type'];
        $courseEnrollmentId = $paymentRequestPayload['course_enrollment_id'];
        $feedUri = $paymentRequestPayload['feed_uri'];
        $courseEnrollment = CourseEnrollment::find($courseEnrollmentId);

        $response = null;
        if ($paymentGatewayType == PaymentTransactionLogHistory::PAYMENT_GATEWAY_EK_PAY) {
            $payload = $this->buildPayload($paymentGatewayType, $courseEnrollment,$feedUri);
            $response = app(EkPayService::class)->paymentByEkPay($payload);
            if (!empty($response)) {
                $data['order_id'] = $payload['payment']['ord_id'];
                $data['mer_trnx_id'] = $payload['payment']['trnx_id'];
                $data['type'] = PaymentTransactionLogHistory::PAYMENT_TYPE_COURSE_ENROLLMENT;
                $data['payment_gateway_type'] = PaymentTransactionLogHistory::PAYMENT_GATEWAY_EK_PAY;
                $data['name'] = $payload['customer']['name'];
                $data['mobile'] = $payload['customer']['mobile'];
                $data['email'] = $payload['customer']['email'];
                $data['trnx_currency'] = $payload['payment']['trnx_currency'];
                $data['amount'] = $payload['payment']['trnx_amt'];
                $data['request_payload'] = $payload;
                $this->storeDataInPaymentHistory($data);
            }
        }

        return $response;
    }

    private function buildPayload(int $paymentGatewayType, CourseEnrollment $courseEnrollment,array $feedUri): array
    {
        /** @var Course $courseInfo */
        $courseInfo = Course::findOrFail($courseEnrollment->course_id);

        $paymentGatewayPayLoad = [];
        if ($paymentGatewayType == PaymentTransactionLogHistory::PAYMENT_GATEWAY_EK_PAY) {
            $paymentGatewayPayLoad = [
                "customer" => [
                    "id" => $courseEnrollment->youth_id,
                    "name" => $courseEnrollment->first_name . " " . $courseEnrollment->last_name,
                    "email" => $courseEnrollment->email,
                    "mobile" => $courseEnrollment->mobile,
                ],
                'payment' => [
                    'trnx_id' => $this->getTrnxId(),
                    'trnx_amt' => $courseInfo->course_fee,
                    'trnx_currency' => config('ekpay.trnx_currency'),
                    'ord_id' => $courseEnrollment->id,
                    'ord_det' => 'Course Enrollment Fee',
                ],
                "feed_uri"=>$feedUri
            ];
        }
        return $paymentGatewayPayLoad;
    }

    private function getTrnxId(): string
    {
        return UuidV4::uuid4();

    }

    public function paymentValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            "payment_gateway_type" => [
                "required",
                Rule::in(array_values(PaymentTransactionLogHistory::PAYMENT_GATEWAYS))
            ],
            "course_enrollment_id" => [
                "required",
                "int",
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

    public function isNotSMSVerified(array $data): bool
    {
        return (bool)CourseEnrollment::where('id', $data['course_enrollment_id'])->whereNull('verification_code_verified_at')->count();
    }

    private function storeDataInPaymentHistory(array $paymentData)
    {
        $paymentHistory = new PaymentTransactionLogHistory();
        $paymentHistory->fill($paymentData);
        $paymentHistory->save();
    }
}
