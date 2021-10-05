<?php

use App\Models\BaseModel;

if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        $config = config("httpclientendpoint");

        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            if ($type == BaseModel::CORE_CLIENT_URL_TYPE) {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["core"]["dev"] : $config["core"]["prod"];
            } elseif ($type == BaseModel::ORGANIZATION_CLIENT_URL_TYPE) {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["organization"]["dev"] : $config["organization"]["prod"];
            } elseif ($type == BaseModel::INSTITUTE_URL_CLIENT_TYPE) {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["institute"]["dev"] : $config["institute"]["prod"];
            } elseif ($type == BaseModel::IDP_SERVER_CLIENT_URL_TYPE) {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["idp_server"]["dev"] : $config["idp_server"]["prod"];
            }

        } else {
            if ($type == BaseModel::CORE_CLIENT_URL_TYPE) {
                return $config["core"]["local"];
            } elseif ($type == BaseModel::ORGANIZATION_CLIENT_URL_TYPE) {
                return $config["organization"]["local"];
            } elseif ($type == BaseModel::INSTITUTE_URL_CLIENT_TYPE) {
                return $config["institute"]["local"];
            } elseif ($type == BaseModel::IDP_SERVER_CLIENT_URL_TYPE) {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["idp_server"]["dev"] : $config["idp_server"]["prod"];
            }
        }
        return "";
    }
}
