<?php

use App\Models\EducationLevel;

return [
    "is_dev_mode" => env("IS_DEVELOPMENT_MODE", false),
    'http_debug' => env("HTTP_DEBUG_MODE", false),
    "should_ssl_verify" => env("IS_SSL_VERIFY", false),
    "http_timeout" => env("HTTP_TIMEOUT", 60),
    "user_cache_ttl" => 86400,

    'nationalities' => [
        '1' => ['en' => 'Bangladeshi', 'bn' => 'Bangladeshi'],
        '2' => ['en' => 'Indian', 'bn' => 'Indian'],
        '3' => ['en' => 'Pakistani', 'bn' => 'Pakistani'],
        '4' => ['en' => 'Nepali', 'bn' => 'Nepali'],
    ],
    'relationship_types' => [
        '1' => "Father",
        '2' => "Mother",
        '3' => "Uncle",
        '4' => "Aunt",
        '5' => "Other",
    ],
    'education_levels' => [
        1 => ['en' => 'PSC/5 Pass', 'bn' => 'PSC/5 Pass'],
        2 => ['en' => 'JSC/JDC/8 Pass', 'bn' => 'JSC/JDC/8 Pass'],
        3 => ['en' => 'Secondary', 'bn' => 'Secondary'],
        4 => ['en' => 'Higher Secondary', 'bn' => 'Higher Secondary'],
        5 => ['en' => 'Diploma', 'bn' => 'Diploma'],
        6 => ['en' => 'Bachelor/Honors', 'bn' => 'Bachelor/Honors'],
        7 => ['en' => 'Masters', 'bn' => 'Masters'],
        8 => ['en' => 'PhD', 'bn' => 'PhD'],
    ],
    'exam_degree_results' => [
        1 => [
            'id' => 1,
            'code' => EducationLevel::RESULT_FIRST_DIVISION,
            'title_en' => 'First Division/Class',
            'title' => 'First Division/Class'
        ],
        2 => [
            'id' => 2,
            'code' => EducationLevel::RESULT_SECOND_DIVISION,
            'title_en' => 'Second  Division/Class',
            'title' => 'Second  Division/Class'
        ],
        3 => [
            'id' => 3,
            'code' => EducationLevel::RESULT_THIRD_DIVISION,
            'title_en' => 'Third Division/Class',
            'title' => 'Third Division/Class'
        ],
        4 => [
            'id' => 4,
            'code' => EducationLevel::RESULT_GRADE,
            'title_en' => 'Grade',
            'title' => 'Grade'
        ],
        5 => [
            'id' => 5,
            'code' => EducationLevel::RESULT_APPEARED,
            'title_en' => 'Appeared',
            'title' => 'Appeared'
        ],
        6 => [
            'id' => 6,
            'code' => EducationLevel::RESULT_ENROLLED,
            'title_en' => 'Enrolled',
            'title' => 'Enrolled'
        ],
        7 => [
            'id' => 7,
            'code' => EducationLevel::RESULT_AWARDED,
            'title_en' => 'Awarded',
            'title' => 'Awarded'
        ],
        8 => [
            'id' => 8,
            'code' => EducationLevel::RESULT_DO_NOT_MENTION,
            'title_en' => 'Do Not Mention',
            'title' => 'Do Not Mention'
        ],
        9 => [
            'id' => 9,
            'code' => EducationLevel::RESULT_PASS,
            'title_en' => 'Pass',
            'title_bn' => 'Pass'
        ],

    ]
];
