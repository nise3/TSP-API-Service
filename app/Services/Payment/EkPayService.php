<?php

namespace App\Services\Payment;

use App\Models\BaseModel;
use Carbon\Carbon;
use http\Url;
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
    public function paymentByEkPay(array $payLoad): mixed
    {
        $customerInfo = $payLoad['customer'];
        $paymentInfo = $payLoad['payment'];
        $feedUri = $payLoad['feed_uri'];
        $ipnInfo = $payLoad['ipn_info'];

        $token = $this->ekPayInit($customerInfo, $paymentInfo, $ipnInfo, $feedUri);
        if (!empty($token)) {
            $token = (config('ekpay.is_sand_box') ? config('ekpay.sand_box.ekpay_base_uri') : config('ekpay.production.ekpay_base_uri')) . '?sToken=' . $token . '&trnsID=' . $paymentInfo['trnx_id'];
        }
        return $token;
    }

    /**
     * @param array $customerInfo
     * @param array $paymentInfo
     * @return mixed
     * @throws RequestException
     */
    private function ekPayInit(array $customerInfo, array $paymentInfo, array $ipnInfo, array $feedUri): mixed
    {
        $time = Carbon::now()->format('Y-m-d H:i:s');

        $customerCleanName = preg_replace('/[^A-Za-z0-9 \-\.]/', '', $customerInfo['name']);

        $ekPayPayload = [
            'mer_info' => [
                'mer_reg_id' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.mer_info.mer_reg_id') : config('ekpay.production.mer_info.mer_reg_id'),
                'mer_pas_key' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.mer_info.mer_pas_key') : config('ekpay.production.mer_info.mer_pas_key'),
            ],
            'feed_uri' => [
                's_uri' => $feedUri['success'] ?? config('ekpay.ek_pay_base_config.feed_uri.success_uri'),
                'f_uri' => $feedUri['failed'] ?? config('ekpay.ek_pay_base_config.feed_uri.fail_uri'),
                'c_uri' => $feedUri['cancel'] ?? config('ekpay.ek_pay_base_config.feed_uri.cancel_uri'),
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
                'ipn_channel' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.ipn_info.ipn_channel') : config('ekpay.production.ipn_info.ipn_channel'),
                'ipn_email' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.ipn_info.ipn_email') : config('ekpay.production.ipn_info.ipn_email'),
                'ipn_uri' => $ipnInfo['ipn_uri'],
            ],
            'mac_addr' => config('ekpay.is_sand_box') ? config('ekpay.sand_box.mac_addr') : config('ekpay.production.mac_addr'),
        ];

        if (config('ekpay.debug')) {
            Log::channel('ek_pay')->info("Youth Name: " . $customerInfo['name'] . ' , Youth Enroll ID: ' . $paymentInfo['ord_id']);
            Log::channel('ek_pay')->info("Ekpay Request PayLoad: " . json_encode($ekPayPayload));
        }

        $url = (config('ekpay.is_sand_box') ? config('ekpay.sand_box.ekpay_base_uri') : config('ekpay.production.ekpay_base_uri')) . "/merchant-api";

        Log::info("ekpay-url:" . $url);

        $res = Http::withoutVerifying()
            ->withHeaders([
                "Content-Type" => 'application/json'
            ])
            ->post($url, $ekPayPayload)
            ->throw()
            ->json(); //secure_token

        Log::info("Http-log: " . json_encode($res));
        return $res['secure_token'] ?? null;

    }
}