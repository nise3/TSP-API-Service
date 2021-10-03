<?php

if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        $config = config("httpclientendpoint");

        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            if ($type == "CORE") {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["core"]["dev"] : $config["core"]["prod"];
            } elseif ($type == "ORGANIZATION") {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["organization"]["dev"] : $config["organization"]["prod"];
            } elseif ($type == "INSTITUTE") {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["institute"]["dev"] : $config["institute"]["prod"];
            } elseif ($type == "IDP_SERVER") {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["idp_server"]["dev"] : $config["idp_server"]["prod"];
            }

        } else {
            if ($type == "CORE") {
                return $config["core"]["local"];
            } elseif ($type == "ORGANIZATION") {
                return $config["organization"]["local"];
            } elseif ($type == "INSTITUTE") {
                return $config["institute"]["local"];
            } elseif ($type == "IDP_SERVER") {
                return env("IS_DEVELOPMENT_MOOD",false) ? $config["idp_server"]["dev"] : $config["idp_server"]["prod"];
            }
        }
        return "";
    }
}
