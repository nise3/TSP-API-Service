<?php

return [
    "core" => [
        "local" => "http://localhost:8000/api/v1/",
        "dev" => "http://core.local:8008/api/v1/",
        "prod" => "https://core.bus-staging.softbdltd.com/api/v1/"
    ],
    "institute" => [
        "local" => "http://localhost:8001/api/v1/",
        "dev" => "http://institute.local:8009/api/v1/",
        "prod" => "https://institute.bus-staging.softbdltd.com/api/v1/"
    ],
    "organization" => [
        "local" => "http://localhost:8002/api/v1/",
        "dev" => "http://organization.local:8010/api/v1/",
        "prod" => "https://org.bus-staging.softbdltd.com/api/v1/"
    ],
    "youth" => [
        "local" => "http://localhost:8003/api/v1/",
        "dev" => "http://youth.local:8011/api/v1/",
        "prod" => "https://youth.bus-staging.softbdltd.com/api/v1/"
    ],
    "cms" => [
        "local" => "http://localhost:8004/api/v1/",
        "dev" => "http://youth.local:8012/api/v1/",
        "prod" => "https://cms.bus-staging.softbdltd.com/api/v1/"
    ],
    "idp_server" => [
        "local" => "",
        "dev" => "https://identity.bus-staging.softbdltd.com/scim2/Users",
        "prod" => "https://identity.bus-staging.softbdltd.com/scim2/Users"
    ]
];
