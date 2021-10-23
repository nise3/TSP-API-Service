<?php

namespace App\Helpers\Classes;

use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthUtility
{
    /**
     * @throws Throwable
     */
    public static function getIdpServerIdFromToken($data, $verify = false)
    {
        $sections = explode('.', $data);

        throw_if((count($sections) < 3), AuthenticationException::class, 'Invalid number of sections of Auth Tokens (<3)', Response::HTTP_BAD_REQUEST);

        list($header, $claims, $signature) = $sections;
        preg_match("/['\"]sub['\"]:['\"](.*?)['\"][,]/", base64_decode($claims), $matches);

        return count($matches) > 1 ? $matches[1] : "";
    }
}
