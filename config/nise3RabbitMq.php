<?php

return [
    'exchanges' => [
        'core' => [
            'name' => 'core.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => 'core.alternate.x',
                'type' => 'fanout',
                'queue' => 'core.alternate.q'
            ],
            'queue' => [
                'demo' => [
                    'name' => 'core.demo.q',
                    'binding' => 'core.demo',
                    'durable' => true,
                    'autoDelete' => false
                ]
            ],
        ],
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
        'organization' => [
            'name' => 'organization.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => 'organization.alternate.x',
                'type' => 'fanout',
                'queue' => 'organization.alternate.q'
            ],
            'queue' => [
                'demo' => [
                    'name' => 'organization.demo.q',
                    'binding' => 'organization.demo',
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
        'cms' => [
            'name' => 'cms.x',
            'type' => 'topic',
            'durable' => true,
            'autoDelete' => false,
            'alternateExchange' => [
                'name' => 'cms.alternate.x',
                'type' => 'fanout',
                'queue' => 'cms.alternate.q'
            ],
            'queue' => [
                'batchCalender' => [
                    'name' => 'cms.batch.calender.q',
                    'binding' => 'cms.batch.calender',
                    'durable' => true,
                    'autoDelete' => false
                ],
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
