<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionLogHistory;
use App\Services\Payment\PaymentService;
use http\Exception\RuntimeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class PaymentController
{
    public PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function payNow(Request $request): JsonResponse
    {
        $paymentValidationData = $this->paymentService->paymentValidator($request)->validate();
        $response = $this->paymentService->paymentProcessing($paymentValidationData);
        $statusCode = !empty($response) ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;

        if ($this->paymentService->isNotSMSVerified($paymentValidationData)) {
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

    public function success(Request $request): JsonResponse
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

    public function fail(Request $request): JsonResponse
    {
        $response = [
            "_response_status" => [
                "status" => false,
                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "Unprocessable Payment Request"
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function cancel(Request $request): JsonResponse
    {
        $response = [
            "_response_status" => [
                "status" => false,
                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "Unprocessable Payment Request"
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function ipnHandler(Request $request)
    {
        Log::channel('ek_pay')->info("IPN RESPONSE: " . json_encode($request->all()));
        DB::beginTransaction();

        $paymentStatus = $this->paymentService->getPaymentStatus($request->msg_code);

        $data['trnx_id'] = $request->trnx_info['trnx_id'];
        $data['payment_instrument_type'] = $request->pi_det_info['pi_type'];
        $data['payment_instrument_name'] = $request->pi_det_info['pi_name'];
        $data['paid_amount'] = $request->trnx_info['trnx_amt'];
        $data['response_message'] = $request->all();
        $data['status'] = $paymentStatus;

        $payment = PaymentTransactionLogHistory::where('mer_trnx_id', $request->trnx_info['mer_trnx_id'])->first();

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
