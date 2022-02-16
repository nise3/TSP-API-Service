<?php

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        return config("httpclientendpoint." . $type);
    }
}

if (!function_exists('formatApiResponse')) {
    /**
     * @param $data
     * @param $startTime
     * @param int $statusCode
     * @return array
     */
    function formatApiResponse($data, $startTime, int $statusCode = 200): array
    {
        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => $startTime,
                "query_time" => $startTime->diffForHumans(Carbon::now())
            ]
        ];
    }
}

if (!function_exists("idpUserErrorMessage")) {

    /**
     * @param $exception
     * @return array
     */
    function idUserErrorMessage($exception): array
    {
        $statusCode = $exception->getCode();
        $errors = [
            '_response_status' => [
                'success' => false,
                'code' => $statusCode,
                "message" => $exception->getMessage(),
                "query_time" => 0
            ]
        ];

        switch ($statusCode) {
            case ResponseAlias::HTTP_UNPROCESSABLE_ENTITY:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $errors['_response_status']['message'] = "Username already exists in IDP";
                return $errors;
            }
            case ResponseAlias::HTTP_NOT_FOUND:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
                $errors['_response_status']['message'] = "IDP user not found";
                return $errors;
            }
            case ResponseAlias::HTTP_UNAUTHORIZED:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_UNAUTHORIZED;
                $errors['_response_status']['message'] = "HTTP 401 Unauthorized Error in IDP server";
                return $errors;
            }
            case ResponseAlias::HTTP_BAD_REQUEST:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_BAD_REQUEST;
                $errors['_response_status']['message'] = "HTTP 400 BAD Request Error in IDP server";
                return $errors;
            }
            case 0:
            {
                $errors['_response_status']['message'] = $exception->getHandlerContext()['error'] ?? " SSL Certificate Error: An expansion of the 400 Bad Request response code, used when the client has provided an invalid client certificate";
                return $errors;
            }
            default:
            {
                return $errors;
            }

        }
    }

//    if (!function_exists("getInstituteId")) {
//        function getInstituteId(): int|null
//        {
//            $authUser = \Illuminate\Support\Facades\Auth::user();
//            return $authUser && $authUser->user_type == BaseModel::INSTITUTE_USER_TYPE && $authUser->institute_id ? $authUser->institute_id : request()->get('institute_id');
//        }
//    }

}

if (!function_exists('generateOtp')) {
    /**
     * @param int $digits
     * @return int
     */
    function generateOtp(int $digits): int
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }
}

if (!function_exists("bearerUserToken")) {

    function bearerUserToken(\Illuminate\Http\Request $request, $headerName = 'User-Token')
    {
        $header = $request->header($headerName);

        $position = strrpos($header, 'Bearer ');

        if ($position !== false) {
            $header = substr($header, $position + 7);
            return strpos($header, ',') !== false ? strstr(',', $header, true) : $header;
        }
    }
}

if (!function_exists("logSelector")) {

    /**
     * @return array
     */
    function logSelector(): array
    {
        if (env('LOG_CHANNEL') == 'elasticsearch') {
            return config('elasticSearchLogConfig');
        }
        return config('lumenDefaultLogConfig');
    }
}
