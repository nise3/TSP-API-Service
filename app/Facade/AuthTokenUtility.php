<?php


namespace App\Facade;


use Illuminate\Support\Facades\Facade;

/**
 * Class AuthUser
 * @package App\Facade
 * @method static string getIdpServerIdFromToken( $data, $verify = false)
 * @see \App\Helpers\Classes\AuthTokenUtilityHandler
 */
class AuthTokenUtility extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'auth_token_utility';
    }

}
