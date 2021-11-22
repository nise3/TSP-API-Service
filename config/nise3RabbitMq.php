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
        'nise3Exchange' => [
            'name' => 'nise3.exchange',
            'type' => 'fanout',
            'routingKey' => 'nise3.routing.key',
            'queues' => [
                'nise3Queue' => [
                    'name' => 'nise3.queue'
                ],
                'courseEnrollment' => [
                    'name' => 'course.enrollment.queue'
                ]
            ]
        ]
    ]
];
