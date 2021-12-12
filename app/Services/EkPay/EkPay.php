<?php

namespace App\Services\EkPay;


use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Http\Redirector;

/**
 * class EkPay
 */
class EkPay
{


    /**
     * @param array $customerInfo
     * @param array $paymentInfo
     * @return Redirector|RedirectResponse
     */
    public function ekPayPaymentGateWay(array $customerInfo, array $paymentInfo): Redirector|RedirectResponse
    {
        $token = $this->ekPayInit($customerInfo, $paymentInfo);
        if (!empty($token)) {
            $token = 'https://sandbox.ekpay.gov.bd/ekpaypg/v1?sToken=' . $token . '&trnsID=' . $paymentInfo['trID'];
        }
        return redirect($token);
    }

    /**
     * @param array $customerInfo
     * @param array $paymentInfo
     * @return mixed
     */
    private function ekPayInit(array $customerInfo, array $paymentInfo): mixed
    {
        $config = config('ekpay');
        $time = Carbon::now()->format('Y-m-d H:i:s');
        $customerCleanName = preg_replace('/[^A-Za-z0-9 \-\.]/', '', $customerInfo['name']);

        $ekPayPayload = [
            'mer_info' => [
                'mer_reg_id' => $config('ek_pay_base_config.mer_info.mer_reg_id'),
                'mer_pas_key' => $config('ek_pay_base_config.mer_info.mer_pas_key'),
            ],
            'feed_uri' => [
                's_uri' => $config('ek_pay_base_config.feed_uri.success_uri'),
                'f_uri' => $config('ek_pay_base_config.feed_uri.fail_uri'),
                'c_uri' => $config('ek_pay_base_config.feed_uri.cancel_uri'),
            ],
            'req_timestamp' => $time . ' GMT+6',
            'cust_info' => [
                'cust_id' => $customerInfo['id'],
                'cust_name' => $customerCleanName,
                'cust_mobo_no' => $customerInfo['mobile'],
                'cust_email' => $customerInfo['email'],
                'cust_mail_addr' => $customerInfo['address'],
            ],
            'trns_info' => [
                'trnx_id' => $paymentInfo['trnx_id'],
                'trnx_amt' => $paymentInfo['amount'],
                'trnx_currency' => $config['trnx_currency'],
                'ord_id' => $paymentInfo['order_id'],
                'ord_det' => $paymentInfo['order_detail'] ?? 'course_fee',
            ],
            'ipn_info' => [
                'ipn_channel' => $config['ek_pay_base_config.ipn_config.ipn_channel'],
                'ipn_email' => $config['ek_pay_base_config.ipn_config.ipn_email'],
                'ipn_uri' => $config['ek_pay_base_config.ipn_config.ipn_uri'],
            ],
            'mac_addr' => env('EKPAY_MAC_ADDRESS', '1.1.1.1'),
        ];
        if ($config['debug']) {
            Log::debug("Youth Name: " . $customerInfo['name'] . ' , Youth Enroll ID: ' . $paymentInfo['orderID']);
            Log::debug(json_encode($ekPayPayload));
        }

        $url = $config['ekpay_base_uri'] . "merchant-api";

        $response = "";
        try {
            /**  Setup curl */
             $ch = curl_init($url);
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                    //'Authorization: '.$authToken,
                    'Content-Type: application/json'
                ),
                CURLOPT_POSTFIELDS => json_encode($ekPayPayload),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ));

            $response = curl_exec($ch);

//            $response=Http::withoutVerifying()
//                      ->withHeaders([
//                          "Content-Type"=>'application/json'
//                      ])
        } catch (Exception $exception) {
            Log::info("EkPay-exception: " . $exception->getTraceAsString());
        }
        $responseData = json_decode($response, TRUE);
        return $responseData['secure_token'];
    }

}
