<?php

return [
    "core" => [
        "local" => "http://localhost:8000/api/v1/",
        "dev" => "http://core.local:8008/api/v1/",
        "prod" => "http://nise3-core-api-service.default/api/v1/"
    ],
    "institute" => [
        "local" => "http://localhost:8001/api/v1/",
        "dev" => "http://institute.local:8009/api/v1/",
        "prod" => "http://nise3-institute.default/api/v1/"
    ],
    "organization" => [
        "local" => "http://localhost:8002/api/v1/",
        "dev" => "http://organization.local:8010/api/v1/",
        "prod" => "http://nise3-org-management.default/api/v1/"
    ],
    "youth" => [
        "local" => "http://localhost:8003/api/v1/",
        "dev" => "http://youth.local:8011/api/v1/",
        "prod" => "http://nise3-youth-management.default/api/v1/"
    ],
    "idp_server" => [
        "local" => "",
        "dev" => "https://is.local:9453/scim2/Users",
        "prod" => "https://identity.bus.softbd.xyz/scim2/Users"
    ]
];
