<?php

namespace App\Services;

use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class RabbitMQService
{
    /**
     * @param RabbitMQQueue $queue
     * @return void
     * @throws AMQPProtocolChannelException
     */
    public function createRabbitMQCommonEntities(RabbitMQQueue $queue): void {

        /** Exchange Queue related variables */
        $exchange = config('queue.connections.rabbitmq.options.exchange.name');
        $exchangeType = config('queue.connections.rabbitmq.options.exchange.type');

        $alternateExchange = config('queue.connections.rabbitmq.options.alternate_exchange.name');
        $alternateExchangeType = config('queue.connections.rabbitmq.options.alternate_exchange.type');
        $alternateQueue = config('queue.connections.rabbitmq.options.alternate_exchange.queue');

        $durable = config('queue.connections.rabbitmq.options.exchange.durable');
        $autoDelete = config('queue.connections.rabbitmq.options.exchange.auto_delete');
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
        if(!$queue->isQueueExists($alternateQueue)){
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
        if(!$queue->isQueueExists($errorQueue)){
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
    public function createQueueAndBindWithoutRetry(RabbitMQQueue $queue, array $payload){
        /** Exchange Queue related variables */
        $exchange = $payload['exchange'];
        $queueName = $payload['queueName'];
        $binding = $payload['binding'];
        $durable = $payload['durable'];
        $autoDelete = $payload['autoDelete'];

        /** Create Queue */
        if(!$queue->isQueueExists($queueName)){
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
    public function createQueueAndBindWithRetry(RabbitMQQueue $queue, array $payload){
        /** Exchange Queue related variables */
        $exchange = $payload['$exchange'];
        $queueName = $payload['$queueName'];
        $binding = $payload['$binding'];
        $durable = $payload['$durable'];
        $autoDelete = $payload['$autoDelete'];

        $dlx = config('queue.connections.rabbitmq.options.dlx.name');
        $dlxType = config('queue.connections.rabbitmq.options.dlx.type');
        $dlq = config('queue.connections.rabbitmq.options.dlx.dlq');
        $dlqMessageTtl = config('queue.connections.rabbitmq.options.dlx.x_message_ttl');

        $dlqArguments = [
            'x-dead-letter-exchange' => $exchange,
            'x-message-ttl' => $dlqMessageTtl
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
        if(!$queue->isQueueExists($dlq)){
            $queue->declareQueue(
                $dlq, true, false, $dlqArguments
            );
        }
        /** Bind DLQ with DLX */
        $queue->bindQueue(
            $dlq, $dlx
        );

        /** Create Queue */
        if(!$queue->isQueueExists($queueName)){
            $queue->declareQueue(
                $queueName, $durable, $autoDelete, $queueArguments
            );
        }
        /** Bind Queue with Exchange. */
        $queue->bindQueue(
            $queueName, $exchange, $binding
        );
    }
}
