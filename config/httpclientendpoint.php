<?php

return [
    "CORE" => env('CORE_API_BASE_URL', 'https://core.bus-staging.softbdltd.com/api/v1/'),
    "INSTITUTE" => env('TSP_API_BASE_URL', 'https://institute.bus-staging.softbdltd.com/api/v1/'),
    "ORGANIZATION" => env('INDUSTRY_API_BASE_URL', 'https://org.bus-staging.softbdltd.com/api/v1/'),
    "YOUTH" => env('YOUTH_API_BASE_URL', '"https://youth.bus-staging.softbdltd.com/api/v1/'),
    "CMS" => env('CMS_API_BASE_URL', '"https://cms.bus-staging.softbdltd.com/api/v1/'),
    'MAIL_SMS' => env('MAIL_API_BASE_URL', 'http://nise-sms-mail.softbd/api/v1/'),
    "IDP_SERVER" => env('WSO2_IDP_BASE_URL', 'https://identity.bus-staging.softbdltd.com/'),
    "IDP_SERVER_USER" => env('WSO2_IDP_BASE_USER_URL', 'https://identity.bus-staging.softbdltd.com/scim2/Users'),
];
