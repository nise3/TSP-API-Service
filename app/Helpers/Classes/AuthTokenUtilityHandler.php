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
        $sections = explode('.', $data);

        throw_if((count($sections) < 3), AuthenticationException::class, 'Invalid number of sections of Auth Tokens (<3)', Response::HTTP_BAD_REQUEST);

        list($header, $claims, $signature) = $sections;

        preg_match("/['\"]sub['\"]:['\"](.*?)['\"][,]/", base64_decode($claims), $matches);

        return count($matches) > 1 ? $matches[1] : "";
    }
    /**
     * @param $data
     * @param bool $verify
     * @return mixed
     * @throws \Throwable
     */
    private function decode($data, bool $verify = false): mixed
    {
        $sections = explode('.', $data);
        throw_if((count($sections) < 3), AuthenticationException::class, 'Invalid number of sections of Tokens (<3)',);

        list($header, $claims, $signature) = $sections;

        $header = json_decode(base64_decode($header));
        $claims = json_decode(base64_decode($claims));

        $signature = json_decode(base64_decode($signature));
        $key = $this->getJwtKey();

        if ($verify) {
            throw_if($this->verify($key, $header, $claims, $signature), AuthenticationException::class,'Signature could not be verified');
        }

        return $claims;
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
}
