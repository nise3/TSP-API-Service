<?php

namespace App\Services\Payment;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EkPayService
{
    /**
     * @param array $payLoad
     * @return string
     * @throws RequestException
     */
    public function paymentByEkPay(array $payLoad): string
    {
        $customerInfo = $payLoad['customer'];
        $paymentInfo = $payLoad['payment'];

        $token = $this->ekPayInit($customerInfo, $paymentInfo);
        if (!empty($token)) {
            $token = config('ekpay.ekpay_base_uri') . '?sToken=' . $token . '&trnsID=' . $paymentInfo['trnx_id'];
        }
        return $token;
    }

    /**
     * @param array $customerInfo
     * @param array $paymentInfo
     * @return mixed
     * @throws RequestException
     */
    private function ekPayInit(array $customerInfo, array $paymentInfo): mixed
    {
        $time = Carbon::now()->format('Y-m-d H:i:s');

        $customerCleanName = preg_replace('/[^A-Za-z0-9 \-\.]/', '', $customerInfo['name']);

        $baseUrl = BaseModel::INSTITUTE_REMOTE_BASE_URL;
        if (request()->getHost() == 'localhost' || request()->getHost() == '127.0. 0.1') {
            $baseUrl = BaseModel::INSTITUTE_LOCAL_BASE_URL;
        }

        $ekPayPayload = [
            'mer_info' => [
                'mer_reg_id' => config('ekpay.ek_pay_base_config.mer_info.mer_reg_id'),
                'mer_pas_key' => config('ekpay.ek_pay_base_config.mer_info.mer_pas_key'),
            ],
            'feed_uri' => [
                's_uri' => $baseUrl . config('ekpay.ek_pay_base_config.feed_uri.success_uri'),
                'f_uri' => $baseUrl . config('ekpay.ek_pay_base_config.feed_uri.fail_uri'),
                'c_uri' => $baseUrl . config('ekpay.ek_pay_base_config.feed_uri.cancel_uri'),
            ],
            'req_timestamp' => $time . ' GMT+6',
            'cust_info' => [
                'cust_id' => $paymentInfo['ord_id'], // order id is customer id
                'cust_name' => $customerCleanName,
                'cust_mobo_no' => $customerInfo['mobile'],
                'cust_email' => $customerInfo['email'],
                'cust_mail_addr' => $customerInfo['address'] ?? " "
            ],
            'trns_info' => [
                'trnx_id' => $paymentInfo['trnx_id'],
                'trnx_amt' => $paymentInfo['trnx_amt'],
                'trnx_currency' => $paymentInfo['trnx_currency'] ?? config('ekpay.trnx_currency'),
                'ord_id' => $paymentInfo['ord_id'],
                'ord_det' => $paymentInfo['ord_det'] ?? 'Course Enrollment Fee',
            ],
            'ipn_info' => [
                'ipn_channel' => config('ekpay.ek_pay_base_config.ipn_info.ipn_channel'),
                'ipn_email' => config('ekpay.ek_pay_base_config.ipn_info.ipn_email'),
                'ipn_uri' => $baseUrl . config('ekpay.ek_pay_base_config.ipn_info.ipn_uri'),
            ],
            'mac_addr' => config('ekpay.mac_addr'),
        ];

        if (config('ekpay.debug')) {
            Log::channel('ek_pay')->info("Youth Name: " . $customerInfo['name'] . ' , Youth Enroll ID: ' . $paymentInfo['ord_id']);
            Log::channel('ek_pay')->info("Ekpay Request PayLoad: " . json_encode($ekPayPayload));
        }

        $url = config('ekpay.ekpay_base_uri') . "/merchant-api";

        return Http::withoutVerifying()
            ->retry(3, 100, function ($exception) {
                return $exception instanceof ConnectionException;
            })
            ->withHeaders([
                "Content-Type" => 'application/json'
            ])
            ->post($url, $ekPayPayload)
            ->throw()
            ->json('secure_token');

    }
}
