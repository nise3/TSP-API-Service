<?php

namespace App\Services\Payment;

use App\Exceptions\HttpErrorException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EkPayService
{

    /**
     * @param array $ekPayPayload
     * @return string|null
     * @throws RequestException
     */
    public function ekPayInit(array $ekPayPayload): ?string
    {

        if (config('ekpay.debug')) {
            Log::channel('ek_pay')->info("Ekpay Request PayLoad: " . json_encode($ekPayPayload, JSON_PRETTY_PRINT));
        }

        $url = (config('ekpay.is_sand_box') ? config('ekpay.sand_box.ekpay_base_uri') : config('ekpay.production.ekpay_base_uri')) . "/merchant-api";

        Log::info("ekpay-url:" . $url);

        $res = Http::withoutVerifying()
            ->withHeaders([
                "Content-Type" => 'application/json'
            ])
            ->timeout(10)
            ->post($url, $ekPayPayload)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json(); //secure_token

        Log::info("Http-log: " . json_encode($res, JSON_PRETTY_PRINT));

        $token = null;
        if (!empty($res['secure_token'])) {
            $token = (config('ekpay.is_sand_box') ? config('ekpay.sand_box.ekpay_base_uri') : config('ekpay.production.ekpay_base_uri')) . '?sToken=' . $res['secure_token'] . '&trnsID=' . $ekPayPayload['trns_info']['trnx_id'];
        }
        return $token;
    }
}
