<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionLog;
use App\Models\PaymentTransactionLogHistory;
use App\Services\Payment\CourseEnrollmentPaymentService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CourseEnrollmentPaymentController extends Controller
{
    public CourseEnrollmentPaymentService $courseEnrollmentPaymentService;

    public function __construct(CourseEnrollmentPaymentService $courseEnrollmentPaymentService)
    {
        $this->courseEnrollmentPaymentService = $courseEnrollmentPaymentService;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function payNowByEkPay(Request $request): JsonResponse
    {

        $paymentValidationData = $this->courseEnrollmentPaymentService->ekPayPaymentValidator($request)->validate();
        $response = $this->courseEnrollmentPaymentService->enrollmentEkPayPaymentProcessing($paymentValidationData);
        $statusCode = !empty($response) ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;

        if ($this->courseEnrollmentPaymentService->isNotSMSVerified($paymentValidationData)) {
            $statusCode = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $response = [
                "errors" => [
                    "sms_verification" => "Sms Verification is not yet done, Please verify it",
                ],
                "_response_status" => [
                    "status" => false,
                    "code" => $statusCode,
                    "message" => "Validation Error"
                ]
            ];

        } else {
            $response = [
                "redirect_url" => !empty($response) ? $response : null,
                "_response_status" => [
                    "status" => !empty($response),
                    "code" => $statusCode,
                    "message" => !empty($response) ? "Success" : "Unprocessable Payment Request"
                ]
            ];
        }

        return Response::json($response, $statusCode);
    }

    public function ekPayPaymentSuccess(Request $request): JsonResponse
    {
        $response = [
            "_response_status" => [
                "status" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Success"
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function ekPayPaymentFail(Request $request): JsonResponse
    {
        $response = [
            "_response_status" => [
                "status" => false,
                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "Payment Cancel"
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function ekPayPaymentCancel(Request $request): JsonResponse
    {
        $response = [
            "_response_status" => [
                "status" => false,
                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "Payment Failed"
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws Throwable
     */
    public function ekPayPaymentIpnHandler(Request $request, string $secretToken)
    {
        Log::channel('ek_pay')->info("IPN RESPONSE: " . json_encode($request->all()));

        if (PaymentService::checkSecretToken($secretToken)) {
            DB::beginTransaction();
            $paymentStatus = PaymentService::getPaymentStatus($request->msg_code);

            $data['trnx_id'] = $request->trnx_info['trnx_id'];
            $data['paid_amount'] = $request->trnx_info['trnx_amt'];
            $data['response_message'] = $request->all();
            $data['status'] = $paymentStatus;

            $payment = PaymentTransactionLog::where('mer_trnx_id', $request->trnx_info['mer_trnx_id'])->first();

            Log::channel("ek_pay")->info("Payment Info in ipnHandler for mer_trnx_id=" . $request->trnx_info['mer_trnx_id'] . json_encode($payment));

            try {
                if ($payment) {
                    $payment->fill($data);
                    $payment->save();
                    $courseEnroll = CourseEnrollment::findOrFail($payment->order_id);
                    $courseEnroll->payment_status = $paymentStatus;
                    $courseEnroll->save();
                    DB::commit();
                }
            } catch (Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
        }

    }
}
