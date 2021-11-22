<?php

use App\Models\EducationLevel;

return [
    'exchangeType' => [
        'direct' => 'direct',
        'topic' => 'topic',
        'fanout' => 'fanout',
        'headers' => 'headers'
    ],
    'exchanges' => [
        'courseEnrollmentExchange' => [
            'name' => 'course.enrollment.exchange',
            'type' => 'fanout',
            'routingKey' => 'course.enrollment.routing.key.1',
            'queues' => [
                'courseEnrollmentQueue1' => [
                    'name' => 'course.enrollment.queue.1'
                ]
            ]
        ]
    ]
];
