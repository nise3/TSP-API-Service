<?php

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            if ($type == BaseModel::CORE_CLIENT_URL_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.core.dev") : config("httpclientendpoint.core.prod");
            } elseif ($type == BaseModel::ORGANIZATION_CLIENT_URL_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.organization.dev") : config("httpclientendpoint.organization.prod");
            } elseif ($type == BaseModel::INSTITUTE_URL_CLIENT_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.institute.dev") : config("httpclientendpoint.institute.prod");
            } elseif ($type == BaseModel::CMS_CLIENT_URL_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.cms.dev") : config("httpclientendpoint.cms.prod");
            } elseif ($type == BaseModel::YOUTH_CLIENT_URL_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.youth.dev") : config("httpclientendpoint.youth.prod");
            } elseif ($type == BaseModel::IDP_SERVER_CLIENT_URL_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.idp_server.dev") : config("httpclientendpoint.idp_server.prod");
            } elseif ($type == BaseModel::MAIL_SMS_SEND) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.mail_sms_send.dev") : config("httpclientendpoint.mail_sms_send.prod");
            }

        } else {
            if ($type == BaseModel::CORE_CLIENT_URL_TYPE) {
                return config("httpclientendpoint.core.local");
            } elseif ($type == BaseModel::ORGANIZATION_CLIENT_URL_TYPE) {
                return config("httpclientendpoint.organization.local");
            } elseif ($type == BaseModel::INSTITUTE_URL_CLIENT_TYPE) {
                return config("httpclientendpoint.institute.local");
            } elseif ($type == BaseModel::YOUTH_CLIENT_URL_TYPE) {
                return config("httpclientendpoint.youth.local");
            } elseif ($type == BaseModel::CMS_CLIENT_URL_TYPE) {
                return config("httpclientendpoint.cms.local");
            } elseif ($type == BaseModel::IDP_SERVER_CLIENT_URL_TYPE) {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.idp_server.dev") : config("httpclientendpoint.idp_server.prod");
            } elseif ($type == BaseModel::MAIL_SMS_SEND) {
                return config("httpclientendpoint.mail_sms_send.local");
            }
        }
        return "";
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

    if (!function_exists("getInstituteId")) {
        function getInstituteId(\Illuminate\Http\Request $request): int|null
        {
            $authUser = \Illuminate\Support\Facades\Auth::user();

            return $authUser && $authUser->institute_id ? $authUser->institute_id : $request->get('institute_id');
        }
    }

}
