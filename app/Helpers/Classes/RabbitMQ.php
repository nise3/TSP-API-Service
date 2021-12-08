<?php

namespace App\Helpers\Classes;

use App\Services\RabbitMQService;
use Exception;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class RabbitMQ
{
    /**
     * @throws AMQPProtocolChannelException
     * @throws Exception
     */
    public function publishEvent(
        RabbitMQConnector $connector, RabbitMQService $rabbitMqService, string $configExchangeName, string $configQueueName, bool $retry = false
    ): void {
        /** Alternate Exchange related variables */
        $alternateExchange = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.alternateExchange.name');
        $alternateExchangeType = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.alternateExchange.type');
        $alternateQueue = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.alternateExchange.queue');

        /** Exchange Queue related variables */
        $exchange = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.name');
        $type = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.type');
        $durable = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.durable');
        $autoDelete = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.autoDelete');
        $exchangeArguments = [
            'alternate-exchange' => $alternateExchange
        ];
        $queueName = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.queue.' . $configQueueName . '.name');
        $binding = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.queue.' . $configQueueName . '.binding');

        /** Set Config to publish the event message */
        config([
            'queue.connections.rabbitmq.options.exchange.name' => $exchange,
            'queue.connections.rabbitmq.options.queue.exchange' => $exchange,
            'queue.connections.rabbitmq.options.exchange.type' => $type,
            'queue.connections.rabbitmq.options.queue.exchange_type' => $type,
            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => $binding
        ]);

        /** Create connection with RabbitMQ server */
        $config = config('queue.connections.rabbitmq');
        $queue = $connector->connect($config);

        /** Create Alternate Exchange, Queue and Bind Queue with Alternate Exchange */
        $alternateExchangePayload = [
            'exchange' => $alternateExchange,
            'type' => $alternateExchangeType,
            'durable' => true,
            'autoDelete' => false,
            'queueName' => $alternateQueue,
            'binding' => ""
        ];
        $rabbitMqService->createExchangeQueueAndBind($queue, $alternateExchangePayload, false);

        /** Create Exchange, Queue and Bind Queue with Exchange */
        $exchangePayload = [
            'exchange' => $exchange,
            'type' => $type,
            'durable' => $durable,
            'autoDelete' => $autoDelete,
            'exchangeArguments' => $exchangeArguments,
            'queueName' => $queueName,
            'binding' => $binding
        ];
        if ($retry) {
            /** DlX-DLQ related variables */
            $dlx = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.dlx.name');
            $dlxType = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.dlx.type');
            $dlq = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.dlx.dlq');
            $messageTtl = config('nise3RabbitMq.exchanges.' . $configExchangeName . '.dlx.x_message_ttl');

            $exchangePayload['dlx'] = $dlx;
            $exchangePayload['dlxType'] = $dlxType;
            $exchangePayload['dlq'] = $dlq;
            $exchangePayload['messageTtl'] = $messageTtl;

            $rabbitMqService->createExchangeQueueAndBind($queue, $exchangePayload, true);
        } else {
            $rabbitMqService->createExchangeQueueAndBind($queue, $exchangePayload, false);
        }
    }
}
