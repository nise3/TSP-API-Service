<?php

namespace App\Services\CommonServices;

use App\Models\BaseModel;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class RabbitMQService
{
    /**
     * @param RabbitMQQueue $queue
     * @return void
     * @throws AMQPProtocolChannelException
     */
    public function createRabbitMQCommonEntities(RabbitMQQueue $queue): void
    {
        /** Exchange Queue related variables */
        $exchange = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.name');
        $exchangeType = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.type');
        $durable = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.durable');
        $autoDelete = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.autoDelete');

        $alternateExchange = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.alternateExchange.name');
        $alternateExchangeType = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.alternateExchange.type');
        $alternateQueue = config('nise3RabbitMq.exchanges.' . BaseModel::SELF_EXCHANGE . '.alternateExchange.queue');

        $exchangeArguments = [
            'alternate-exchange' => $alternateExchange
        ];

        $errorExchange = env('RABBITMQ_ERROR_EXCHANGE_NAME', 'error.x');
        $errorExchangeType = env('RABBITMQ_ERROR_EXCHANGE_TYPE', 'fanout');
        $errorQueue = env('RABBITMQ_ERROR_QUEUE_NAME', 'error.q');

        /** Create Alternate Exchange */
        if (!$queue->isExchangeExists($alternateExchange)) {
            $queue->declareExchange(
                $alternateExchange, $alternateExchangeType, $durable, $autoDelete
            );
        }
        /** Create Alternate Queue */
        if (!$queue->isQueueExists($alternateQueue)) {
            $queue->declareQueue(
                $alternateQueue, $durable, $autoDelete
            );
        }
        /** Bind Alternate Queue with Alternate Exchange. */
        $queue->bindQueue(
            $alternateQueue, $alternateExchange
        );

        /** Create Exchange */
        if (!$queue->isExchangeExists($exchange)) {
            $queue->declareExchange(
                $exchange, $exchangeType, $durable, $autoDelete, $exchangeArguments
            );
        }

        /** Create Error Exchange */
        if (!$queue->isExchangeExists($errorExchange)) {
            $queue->declareExchange(
                $errorExchange, $errorExchangeType, true, false
            );
        }
        /** Create Error Queue */
        if (!$queue->isQueueExists($errorQueue)) {
            $queue->declareQueue(
                $errorQueue, true, false
            );
        }
        /** Bind Error Queue with Error Exchange. */
        $queue->bindQueue(
            $errorQueue, $errorExchange
        );
    }

    /**
     * @param RabbitMQQueue $queue
     * @param array $payload
     * @return void
     * @throws AMQPProtocolChannelException
     */
    public function createQueueAndBindWithoutRetry(RabbitMQQueue $queue, array $payload)
    {
        /** Exchange Queue related variables */
        $exchange = $payload['exchange'];
        $durable = $payload['durable'] ?? true;
        $autoDelete = $payload['autoDelete'] ?? false;
        $queueName = $payload['queueName'];
        $binding = $payload['binding'] ?? "";

        /** Create Queue */
        if (!$queue->isQueueExists($queueName)) {
            $queue->declareQueue(
                $queueName, $durable, $autoDelete
            );
        }

        /** Bind Queue with Exchange. */
        $queue->bindQueue(
            $queueName, $exchange, $binding
        );
    }

    /**
     * @param RabbitMQQueue $queue
     * @param array $payload
     * @return void
     * @throws AMQPProtocolChannelException
     */
    public function createQueueAndBindWithRetry(RabbitMQQueue $queue, array $payload)
    {
        /** Exchange Queue related variables */
        $exchange = $payload['exchange'];
        $durable = $payload['durable'] ?? true;
        $autoDelete = $payload['autoDelete'] ?? false;
        $queueName = $payload['queueName'];
        $binding = $payload['binding'] ?? "";

        /** DLX-DLQ related variables */
        $dlx = $payload['dlx'];
        $dlxType = $payload['dlxType'];
        $dlq = $payload['dlq'];
        $dlqMessageTtl = $payload['messageTtl'];

        $dlqArguments = [
            'x-dead-letter-exchange' => $exchange,
            'x-message-ttl' => (int)$dlqMessageTtl
        ];
        $queueArguments = [
            'x-dead-letter-exchange' => $dlx
        ];

        /** Create DLX */
        if (!$queue->isExchangeExists($dlx)) {
            $queue->declareExchange(
                $dlx, $dlxType, true, false
            );
        }
        /** Create DLQ */
        if (!$queue->isQueueExists($dlq)) {
            $queue->declareQueue(
                $dlq, true, false, $dlqArguments
            );
        }
        /** Bind DLQ with DLX */
        $queue->bindQueue(
            $dlq, $dlx
        );

        /** Create Queue */
        if (!$queue->isQueueExists($queueName)) {
            $queue->declareQueue(
                $queueName, $durable, $autoDelete, $queueArguments
            );
        }
        /** Bind Queue with Exchange. */
        $queue->bindQueue(
            $queueName, $exchange, $binding
        );
    }

    /**
     * @param RabbitMQQueue $queue
     * @param array $payload
     * @param bool $retry
     * @return void
     * @throws AMQPProtocolChannelException
     */
    public function createExchangeQueueAndBind(RabbitMQQueue $queue, array $payload, bool $retry = false)
    {
        $exchange = $payload['exchange'];
        $exchangeType = $payload['type'];
        $durable = $payload['durable'] ?? true;
        $autoDelete = $payload['autoDelete'] ?? false;
        $exchangeArguments = $payload['exchangeArguments'] ?? [];

        /** Create Exchange */
        if (!$queue->isExchangeExists($exchange)) {
            $queue->declareExchange(
                $exchange, $exchangeType, $durable, $autoDelete, $exchangeArguments
            );
        }

        if ($retry) {
            $this->createQueueAndBindWithRetry($queue, $payload);
        } else {
            $this->createQueueAndBindWithoutRetry($queue, $payload);
        }
    }
}
