<?php

use App\Models\EducationLevel;

return [
    'exchangeType' => [
        'direct' => 'direct',
        'topic' => 'topic',
        'fanout' => 'fanout',
        'headers' => 'headers'
    ],
    'exchange' => [
        'mailSms' => [
            'name' => 'mail.sms.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'queue' => [
                'mail' => [
                    'name' => 'mail.q',
                    'binding' => 'mail',
                    'durable' => true,
                    'autoDelete' => false,
                ],
                'sms' => [
                    'name' => 'sms.q',
                    'binding' => 'sms',
                    'durable' => true,
                    'autoDelete' => false,
                ]
            ]
        ]
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
