<?php

use App\Models\EducationLevel;

return [
    'exchangeType' => [
        'direct' => 'direct',
        'topic' => 'topic',
        'fanout' => 'fanout',
        'headers' => 'headers'
    ],
    'queue' => [
        'courseEnrollment' => [
            'name' => 'institute.course.enrollment.q',
            'binding' => 'institute.course.enrollment',
        ]
    ],
    'consume' => [

    ]
];
