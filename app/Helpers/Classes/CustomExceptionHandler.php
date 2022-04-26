<?php


namespace App\Helpers\Classes;


use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class CustomExceptionHandler
{
    /**
     * @throws Exception
     */
    public static function customHttpResponseMessage($messageBody)
    {

        $body = json_decode($messageBody, true);
        $code = $body['_response_status']['code'];
        $message = [];
        if (!empty($body['errors'])) {
            $message["errors"] = $body['errors'];
        }
        if (!empty($body['_response_status']['message'])) {
            $message["message"] = $body['_response_status']['message'];
        }
        throw new Exception(json_encode($message), $code);
    }
}
