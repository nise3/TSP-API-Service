<?php

return [
    'exchanges' => [
        'institute' => [
            'name' => 'institute.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => 'institute.alternate.x',
                'type' => 'fanout',
                'queue' => 'institute.alternate.q'
            ],
            'queue' => [
                'courseEnrollment' => [
                    'name' => 'institute.course.enrollment.q',
                    'binding' => 'institute.course.enrollment',
                    'durable' => true,
                    'autoDelete' => false
                ]
            ],
        ],
        'youth' => [
            'name' => 'youth.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => 'youth.alternate.x',
                'type' => 'fanout',
                'queue' => 'youth.alternate.q'
            ],
            'queue' => [
                'courseEnrollment' => [
                    'name' => 'youth.course.enrollment.q',
                    'binding' => 'youth.course.enrollment',
                    'durable' => true,
                    'autoDelete' => false
                ]
            ],
        ],
        'mailSms' => [
            'name' => 'mail.sms.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => 'mail.sms.alternate.x',
                'type' => 'fanout',
                'queue' => 'mail.sms.alternate.q'
            ],
            'dlx' => [
                'name' => 'mail.sms.dlx',
                'type' => 'fanout',
                'dlq' => 'mail.sms.dlq',
                'x_message_ttl' => 120000
            ],
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
    'consume' => 'institute.course.enrollment.q'
];
