<?php

namespace App\Http\Controllers;

use App\Facade\ServiceToServiceCall;
use App\Models\PaymentTransactionHistory;
use App\Models\PaymentTransactionLog;
use App\Models\RplApplication;
use App\Services\Payment\PaymentService;
use App\Services\Payment\RplApplicationCertificationPaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplApplicationCertificationPaymentController extends Controller
{
    public RplApplicationCertificationPaymentService $rtoApplicationCertificationPaymentService;
    public Carbon $startTime;

    /**
     * @param RplApplicationCertificationPaymentService $rtoApplicationCertificationPaymentService
     */
    public function __construct(RplApplicationCertificationPaymentService $rtoApplicationCertificationPaymentService)
    {
        $this->rtoApplicationCertificationPaymentService = $rtoApplicationCertificationPaymentService;
        $this->startTime = Carbon::now();
    }

    /**
     * @throws ValidationException|Throwable
     */
    public function paymentViaEkPay(Request $request): JsonResponse
    {
        $paymentValidationData = $this->rtoApplicationCertificationPaymentService->paymentValidator($request)->validate();
        $response = $this->rtoApplicationCertificationPaymentService->paymentProcessingViaEkPay($paymentValidationData);
        $statusCode = !empty($response) ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
        $response = [
            "redirect_url" => !empty($response) ? $response : null,
            "_response_status" => [
                "status" => !empty($response),
                "code" => $statusCode,
                "message" => !empty($response) ? "Success" : "Unprocessable Payment Request"
            ]
        ];
        return Response::json($response, $statusCode);
    }

    /**
     * @throws Throwable
     */
    public function ipnHandler(Request $request, string $secretToken)
    {

        Log::channel('ek_pay')->info("IPN RESPONSE(Youth Assessment): " . json_encode($request->all()));

        if (PaymentService::checkSecretToken($secretToken)) {
            DB::beginTransaction();
            [$paymentStatus, $youthAssessmentStatus] = PaymentService::getPaymentAndEnrollmentStatus($request->msg_code);

            $data['trnx_id'] = $request->trnx_info['trnx_id'];
            $data['paid_amount'] = $request->trnx_info['trnx_amt'];
            $data['response_message'] = $request->all();
            $data['status'] = $paymentStatus;
            $data['transaction_completed_at'] = Carbon::now();

            $payment = PaymentTransactionLog::where('mer_trnx_id', $request->trnx_info['mer_trnx_id'])->first();

            Log::channel("ek_pay")->info("Payment Info in ipnHandler for mer_trnx_id=" . $request->trnx_info['mer_trnx_id'] . json_encode($payment));

            try {
                if ($payment) {
                    $payment->fill($data);
                    $payment->save();

                    $youthAssessment = RplApplication::findOrFail($payment->payment_purpose_related_id);
                    $youthAssessment->payment_status = $paymentStatus;
                    $youthAssessment->payment_date = Carbon::now();
                    $youthAssessment->save();

                    if ($paymentStatus == PaymentTransactionHistory::PAYMENT_SUCCESS) {
                        $youth = ServiceToServiceCall::getYouthProfilesByIds([$youthAssessment->youth_id])[0];
                        $paymentHistoryPayload = $payment->toArray();
                        $paymentHistoryPayload['customer_name'] = $youth['first_name'] . " " . $youth['last_name'];
                        $paymentHistoryPayload['customer_email'] = $youth['email'];
                        $paymentHistoryPayload['customer_mobile'] = $youth['mobile'];
                        $paymentHistoryPayload['status'] = PaymentTransactionHistory::PAYMENT_SUCCESS;
                        $paymentHistory = new PaymentTransactionHistory();
                        $paymentHistory->fill($paymentHistoryPayload);
                        $paymentHistory->save();
                        $payment->payment_transaction_history_id = $paymentHistory->id;
                        $payment->save();

                        $this->rtoApplicationCertificationPaymentService->confirmationMailAndSmsSend($paymentHistoryPayload);
                    }

                    DB::commit();
                }
            } catch (Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
        } else {
            Log::debug('ipn-handler-secret-token-info', $request->all());
        }

    }
}
