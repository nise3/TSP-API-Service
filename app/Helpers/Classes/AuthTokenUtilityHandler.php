<?php

namespace App\Helpers\Classes;

use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthTokenUtilityHandler
{
    private const _WSO2_KEY = '';

    /**
     * @param $data
     * @param false $verify
     * @return mixed
     * @throws Throwable
     */
    public function getIdpServerIdFromToken($data, bool $verify = false): mixed
    {
        $payload = $this->decode($data);
        return $payload->sub;
    }
    /**
     * @param $data
     * @param false $verify
     * @return mixed
     * @throws Throwable
     */
    public function getIdpServerUserTypeFromToken($data, bool $verify = false): mixed
    {
        $payload = $this->decode($data);

        return $payload->user_type;
    }

    /**
     * @return string
     */
    private function getJwtKey(): string
    {
        return self::_WSO2_KEY;
    }

    /**
     * Verify Signature
     */
    private function verify($key, $header, $claims, $signature): bool
    {
        return true;
    }


    private function decode($token){

        $tks = explode('.', $token);

        throw_if((count($tks) < 3), AuthenticationException::class, 'Invalid number of sections of Tokens (<3)',);

        list($header, $body, $signature) = $tks;
        $input=$body;
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        $input = (base64_decode(strtr($input, '-_', '+/')));

        $max_int_length = strlen((string) PHP_INT_MAX) - 1;
        $json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);

        return json_decode($json_without_bigints);
    }
}
