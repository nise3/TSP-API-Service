<?php

use \App\Models\BaseModel;

return [
    'exchanges' => [
        BaseModel::SELF_EXCHANGE => [
            'name' => BaseModel::SELF_EXCHANGE.'.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => BaseModel::SELF_EXCHANGE.'.alternate.x',
                'type' => 'fanout',
                'queue' => BaseModel::SELF_EXCHANGE.'.alternate.q'
            ],
            'queue' => [
                'courseEnrollment' => [
                    'name' => BaseModel::SELF_EXCHANGE.'.course.enrollment.q',
                    'binding' => BaseModel::SELF_EXCHANGE.'.course.enrollment',
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
    ],
    'consume' => 'youth.course.enrollment.q'
];
