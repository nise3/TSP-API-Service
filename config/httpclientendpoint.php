<?php

return [
    "core" => [
        "local" => "http://localhost:8000/api/v1/",
        "dev" => "http://nise-core.softbd/api/v1/",
        "prod" => "https://core.bus-staging.softbdltd.com/api/v1/"
    ],
    "institute" => [
        "local" => "http://localhost:8001/api/v1/",
        "dev" => "http://nise-tsp.softbd/api/v1/",
        "prod" => "https://institute.bus-staging.softbdltd.com/api/v1/"
    ],
    "organization" => [
        "local" => "http://localhost:8002/api/v1/",
        "dev" => "http://nise-industry.softbd/api/v1/",
        "prod" => "https://org.bus-staging.softbdltd.com/api/v1/"
    ],
    "youth" => [
        "local" => "http://localhost:8003/api/v1/",
        "dev" => "http://nise-youth.softbd/api/v1/",
        "prod" => "https://youth.bus-staging.softbdltd.com/api/v1/"
    ],
    "cms" => [
        "local" => "http://localhost:8004/api/v1/",
        "dev" => "http://nise-cms.softbd/api/v1/",
        "prod" => "https://cms.bus-staging.softbdltd.com/api/v1/"
    ],
    "idp_server" => [
        "local" => "",
        "dev" => "https://192.168.13.206:9448/scim2/Users",
        "prod" => "https://identity.bus-staging.softbdltd.com/scim2/Users"
    ],
    'mail_sms_send'=>[
        "local" => "http://localhost:8015/api/v1/",
        "dev" => "http://nise-sms-mail.softbd/api/v1/",
        "prod" => ""
    ]
];
