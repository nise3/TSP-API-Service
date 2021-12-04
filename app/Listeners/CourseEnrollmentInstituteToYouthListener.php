<?php

namespace App\Listeners;

use App\Services\RabbitMQService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class CourseEnrollmentInstituteToYouthListener implements ShouldQueue
{
    private RabbitMQConnector $connector;
    private RabbitMQService $rabbitmqService;

    /** Set rabbitmq config where this event is going to publish */
    private const EXCHANGE_CONFIG_NAME = 'youth';
    private const QUEUE_CONFIG_NAME = 'courseEnrollment';

    /**
     * @throws Exception
     */
    public function __construct(RabbitMQConnector $connector, RabbitMQService $rabbitmqService)
    {
        $this->connector = $connector;
        $this->rabbitmqService = $rabbitmqService;
        $this->publishEvent();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function publishEvent(): void
    {
        /** Alternate Exchange related variables */
        $alternateExchange = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.alternateExchange.name');
        $alternateExchangeType = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.alternateExchange.type');
        $alternateQueue = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.alternateExchange.queue');

        /** Exchange Queue related variables */
        $exchange = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.name');
        $type = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.type');
        $durable = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.durable');
        $autoDelete = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.autoDelete');
        $exchangeArguments = [
            'alternate-exchange' => $alternateExchange
        ];
        $queueName = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.queue.' . self::QUEUE_CONFIG_NAME . '.name');
        $binding = config('nise3RabbitMq.exchanges.' . self::EXCHANGE_CONFIG_NAME . '.queue.' . self::QUEUE_CONFIG_NAME . '.binding');

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
        $queue = $this->connector->connect($config);

        /** Create Alternate Exchange, Queue and Bind Queue with Alternate Exchange */
        $payload = [
            'exchange' => $alternateExchange,
            'type' => $alternateExchangeType,
            'durable' => true,
            'autoDelete' => false,
            'queueName' => $alternateQueue,
            'binding' => ""
        ];
        $this->rabbitmqService->createExchangeQueueAndBind($queue, $payload, false);

        /** Create Exchange, Queue and Bind Queue with Exchange */
        $payload = [
            'exchange' => $exchange,
            'type' => $type,
            'durable' => $durable,
            'autoDelete' => $autoDelete,
            'exchangeArguments' => $exchangeArguments,
            'queueName' => $queueName,
            'binding' => $binding
        ];
        $this->rabbitmqService->createExchangeQueueAndBind($queue, $payload, false);


        /*dd(config('queue.connections.rabbitmq.options.exchange.name'),
            config('queue.connections.rabbitmq.options.queue.exchange'),
            config('queue.connections.rabbitmq.options.exchange.type'),
            config('queue.connections.rabbitmq.options.queue.exchange_type'),
            config('queue.connections.rabbitmq.options.queue.exchange_routing_key')
        );*/
    }

    public function handle($event)
    {

    }
}
