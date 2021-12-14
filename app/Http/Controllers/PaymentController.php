<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionLogHistory;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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
     * @throws \Throwable
     */
    public function payNow(Request $request): JsonResponse
    {
        $paymentValidationData = $this->paymentService->paymentValidator($request)->validate();

        /** Sms Verification Check before payment */
        // throw_if($this->paymentService->isNotSMSVerified($paymentValidationData), ValidationException::class, "SMS verification is not done yet, please verify.");

        $response = $this->paymentService->paymentProcessing($paymentValidationData);
        $response = [
            "redirect_url" => !empty($response) ? $response : null,
            "_response_status" => [
                "status" => !empty($response),
                "code" => !empty($response) ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                "message" => !empty($response) ? "Success" : "Unprocessable Payment Request"
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function success(Request $request)
    {
        Log::info("success- " . json_encode($request->all()));
    }

    public function fail(Request $request)
    {
        dd($request->all());
    }

    public function cancel(Request $request)
    {
        dd($request->all());
    }

    public function ipnHandler(Request $request)
    {
        if (!empty($request)) {
            Log::debug("=========================================");

            Log::debug("SandBox Request: ");
            Log::debug($request);

            Log::debug("=========================================");
        }

        Log::debug("=============Debug=============");
        Log::debug($request->msg_code);
        Log::debug($request->cust_info['cust_id']);
        Log::debug("===============================");


        if ($request->msg_code == 1020) {

            $youthCourseEnroll = CourseEnrollment::findOrFail($request->cust_info['cust_id']);
            $youthCourseEnroll->payment_status = CourseEnrollment::PAYMENT_STATUS_PAID;

            $mailSubject = "Your payment successfully complete";
            $youthEmailAddress = $request->cust_info['cust_email'];
            $mailMsg = "Congratulation! Your payment successfully completed.";
            $youthName = $youthCourseEnroll->name;

            //TODO: MAil sending after transaction

        }

        $data['trnx_id'] = $request->trnx_info['trnx_id'];
        $data['payment_instrument_type']=$request->pi_det_info['pi_type'];
        $data['payment_instrument_name']=$request->pi_det_info['pi_name'];
        $data['amount'] = $request->trnx_info['trnx_amt'];
        $data['response_message'] = $request->all();
        $data['status'] = $request->msg_code == 1020 ? PaymentTransactionLogHistory::PAYMENT_SUCCESS : PaymentTransactionLogHistory::PAYMENT_PENDING;
        $payment = PaymentTransactionLogHistory::where('mer_trnx_id', $request->trnx_info['mer_trnx_id'])->first();

        if($payment){
            $payment->fill($data);
            $payment->save();
        }
    }


}
