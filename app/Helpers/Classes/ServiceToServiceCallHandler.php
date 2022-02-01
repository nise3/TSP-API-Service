<?php

namespace App\Helpers\Classes;

use App\Models\BaseModel;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceToServiceCallHandler
{

    /**
     * @param string $idpUserId
     * @return mixed
     * @throws RequestException
     */
    public function getAuthUserWithRolePermission(string $idpUserId): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'auth-user-info';
        $userPostField = [
            "idp_user_id" => $idpUserId
        ];

        $responseData = Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->post($url, $userPostField)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                throw $e;
            })
            ->json('data');


        Log::info("userInfo:" . json_encode($responseData));

        return $responseData;
    }

    /**
     * @param int $industryAssociationId
     * @return mixed
     * @throws RequestException
     */
    public function getIndustryAssociationCode(int $industryAssociationId): mixed
    {
        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/industry-associations/' . $industryAssociationId . '/get-code';

        $responseData = Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->get($url)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                throw $e;
            })
            ->json('data');


        Log::info("industry_association_id:" . json_encode($responseData));

        return $responseData;
    }

    public function getIndustryAssociationData(int $industryAssociationId): mixed
    {
        $url = clientUrl(BaseModel::ORGANIZATION_CLIENT_URL_TYPE) . 'service-to-service-call/industry-associations/' . $industryAssociationId;

        $responseData = Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug'),
                'timeout' => config('nise3.http_timeout'),
            ])
            ->get($url)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                throw $e;
            })
            ->json('data');


        Log::info("industry_association_id:" . json_encode($responseData));

        return $responseData;
    }
}
