<?php

namespace App\Services\Payment;

use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use App\Models\PaymentTransactionHistory;
use App\Models\PaymentTransactionLog;
use Carbon\Carbon;

/**
 * class PaymentService
 */
class PaymentService
{

    public function paymentProcessing(array $payload, int $paymentGatewayType)
    {
        $response = null;
        if ($paymentGatewayType == PaymentTransactionHistory::PAYMENT_GATEWAY_EK_PAY) {
            $response = $this->ekPayPaymentProcessing($payload, $paymentGatewayType);
        }
        return $response;
    }

    /**
     * @param array $payload
     * @param int $paymentGatewayType
     * @return mixed
     */
    private function ekPayPaymentProcessing(array $payload, int $paymentGatewayType): mixed
    {
        $parts = explode('/', $payload['ipn_info']['ipn_uri']);
        $ipnUriSecretToken = end($parts);
        $response = app(EkPayService::class)->paymentByEkPay($payload);

        if (!empty($response)) {
            $data['invoice'] = $payload['invoice'];
            $data['mer_trnx_id'] = $payload['payment']['trnx_id'];
            $data['payment_purpose_related_id'] = $payload['payment_purpose_related_id'];
            $data['payment_purpose_code'] = $payload['payment_purpose_code'];
            $data['payment_gateway_type'] = $paymentGatewayType;
            $data['trnx_currency'] = $payload['payment']['trnx_currency'];
            $data['amount'] = $payload['payment']['trnx_amt'];
            $data['ipn_uri_secret_token'] = $ipnUriSecretToken;
            $data['request_payload'] = $payload;
            $data['transaction_created_at'] = Carbon::now();
            $this->storeDataInPaymentLog($data);
        }
        return $response;
    }


    private function storeDataInPaymentLog(array $paymentLogData)
    {
        $paymentLog = new PaymentTransactionLog();
        $paymentLog->fill($paymentLogData);
        $paymentLog->save();
    }

    public function storeDataInPaymentTransactionHistory(array $paymentData)
    {
        $paymentHistory = new PaymentTransactionHistory();
        $paymentHistory->fill($paymentData);
        $paymentHistory->save();
    }

    /**
     * @param string $msgCode
     * @return array
     */
    public static function getPaymentAndEnrollmentStatus(string $msgCode): array
    {
        if ($msgCode == PaymentTransactionHistory::TRANSACTION_COMPLETED_SUCCESSFULLY) {
            return [
                PaymentTransactionHistory::PAYMENT_SUCCESS,
                BaseModel::ROW_STATUS_ACTIVE
            ];
        } elseif ($msgCode == PaymentTransactionHistory::TRANSACTION_COMPLETED_FAIL) {
            return [
                PaymentTransactionHistory::PAYMENT_FAIL,
                BaseModel::ROW_STATUS_FAILED
            ];
        } elseif ($msgCode == PaymentTransactionHistory::TRANSACTION_COMPLETED_CANCEL) {
            return [
                PaymentTransactionHistory::PAYMENT_CANCEL,
                BaseModel::ROW_STATUS_REJECTED
            ];
        } else {
            return [
                PaymentTransactionHistory::PAYMENT_PENDING,
                BaseModel::ROW_STATUS_PENDING
            ];
        }

    }

    public static function checkSecretToken(string $secretToken): bool
    {
        return (bool)PaymentTransactionLog::where('ipn_uri_secret_token', $secretToken)->count('ipn_uri_secret_token');
    }

}
