<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Lumen's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Lumen. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'queue' => env('RABBITMQ_QUEUE','institute.q'),
            'connection' => PhpAmqpLib\Connection\AMQPLazyConnection::class,
            'hosts' => [
                [
                    'host' => env('RABBITMQ_HOST','localhost'),
                    'port' => env('RABBITMQ_PORT', 5672),
                    'user' => env('RABBITMQ_USER','guest'),
                    'password' => env('RABBITMQ_PASSWORD','guest'),
                    'vhost' => env('RABBITMQ_VHOST', '/'),
                ],
            ],
            'options' => [
                'queue' => [
                    'exchange' => env('RABBITMQ_EXCHANGE_NAME','institute.x'),
                    'exchange_type' => env('RABBITMQ_EXCHANGE_TYPE','fanout'),
                    'exchange_routing_key' => env('EXCHANGE_ROUTING_KEY','institute.routing.key'),
                    'prioritize_delayed_messages' =>  false,
                    'queue_max_priority' => 10,

                    'enable_retry_limit' => env('RABBITMQ_ENABLE_RETRY_LIMIT', true),
                    'retry_limit' => env('RABBITMQ_RETRY_LIMIT', 5),
                    'error_exchange_name' => env('RABBITMQ_ERROR_EXCHANGE_NAME', 'error.x'),
                    'error_queue_name' => env('RABBITMQ_ERROR_QUEUE_NAME', 'error.q')
                ],
                'exchange' => [
                    'name' => env('RABBITMQ_EXCHANGE_NAME','institute.x'),
                    'type' => env('RABBITMQ_EXCHANGE_TYPE','topic'),
                    'passive' => env('RABBITMQ_EXCHANGE_PASSIVE', false),
                    'durable' => env('RABBITMQ_EXCHANGE_DURABLE', true),
                    'auto_delete' => env('RABBITMQ_EXCHANGE_AUTODELETE', false),
                    'arguments' => env('RABBITMQ_EXCHANGE_ARGUMENTS', 'default'),
                    'declare' => env('RABBITMQ_EXCHANGE_DECLARE', true),
                ],
                'alternate_exchange' => [
                    'name' => env('RABBITMQ_ALTERNATE_EXCHANGE', 'institute.alternate.x'),
                    'type' => env('RABBITMQ_ALTERNATE_EXCHANGE_TYPE','fanout'),
                    'queue' => env('RABBITMQ_ALTERNATE_QUEUE','institute.alternate.q')
                ],
                'dlx' => [
                    'name' => env('RABBITMQ_RETRY_DLX', 'institute.dlx'),
                    'type' => env('RABBITMQ_RETRY_DLX_TYPE', 'fanout'),
                    'dlq' => env('RABBITMQ_RETRY_DLQ', 'institute.dlq'),
                    'x_message_ttl' => env('RABBITMQ_RETRY_MESSAGE_TTL', 120000)
                ],
                'ssl_options' => [
                    'cafile' => env('RABBITMQ_SSL_CAFILE', null),
                    'local_cert' => env('RABBITMQ_SSL_LOCALCERT', null),
                    'local_key' => env('RABBITMQ_SSL_LOCALKEY', null),
                    'verify_peer' => env('RABBITMQ_SSL_VERIFY_PEER', true),
                    'passphrase' => env('RABBITMQ_SSL_PASSPHRASE', null),
                ],
            ],
        ],

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => env('QUEUE_TABLE', 'jobs'),
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('QUEUE_REDIS_CONNECTION', 'default'),
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => env('QUEUE_FAILED_TABLE', 'failed_jobs'),
    ],

];
