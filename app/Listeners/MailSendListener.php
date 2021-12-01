<?php

namespace App\Listeners;

use App\Services\RabbitMQService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class MailSendListener implements ShouldQueue
{
    private RabbitMQConnector $connector;
    private RabbitMQService $rabbitmqService;

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
     * @throws Exception
     * @return void
     */
    private function publishEvent(): void
    {
        $config = config('queue.connections.rabbitmq');
        $queue = $this->connector->connect($config);

        /** Alternate Exchange related variables */
        $alternateExchange = config('nise3RabbitMq.exchanges.mailSmsExchange.alternateExchange.name');
        $alternateExchangeType = config('nise3RabbitMq.exchanges.mailSmsExchange.alternateExchange.type');
        $alternateQueue = config('nise3RabbitMq.exchanges.mailSmsExchange.alternateExchange.queue');

        /** Exchange Queue related variables */
        $exchange = config('nise3RabbitMq.exchanges.mailSmsExchange.name');
        $type = config('nise3RabbitMq.exchanges.mailSmsExchange.type');
        $durable = config('nise3RabbitMq.exchanges.mailSmsExchange.durable');
        $autoDelete = config('nise3RabbitMq.exchanges.mailSmsExchange.autoDelete');
        $exchangeArguments = [
            'alternate-exchange' => $alternateExchange
        ];
        $queueName = config('nise3RabbitMq.exchanges.mailSmsExchange.queue.mail.name');
        $binding = config('nise3RabbitMq.exchanges.mailSmsExchange.queue.mail.binding');

        /** DlX-DLQ related variables */
        $dlx = config('nise3RabbitMq.exchanges.mailSmsExchange.dlx.name');
        $dlxType = config('nise3RabbitMq.exchanges.mailSmsExchange.dlx.type');
        $dlq = config('nise3RabbitMq.exchanges.mailSmsExchange.dlx.dlq');
        $messageTtl = config('nise3RabbitMq.exchanges.mailSmsExchange.dlx.x_message_ttl');

        /** Create Alternate Exchange, Queue and Bind Queue for Mail-SMS Exchange */
        $payload = [
            'exchange' => $alternateExchange,
            'type' => $alternateExchangeType,
            'durable' => true,
            'autoDelete' => false,
            'queueName' => $alternateQueue,
            'binding' => ""
        ];
        $this->rabbitmqService->createExchangeQueueAndBind($queue, $payload, false);

        /** Create Exchange, Queue and Bind Queue with Retry by using DLX-DLQ for RETRY mechanism */
        $payload = [
            'exchange' => $exchange,
            'type' => $type,
            'durable' => $durable,
            'autoDelete' => $autoDelete,
            'exchangeArguments' => $exchangeArguments,
            'queueName' => $queueName,
            'binding' => $binding,
            'dlx' => $dlx,
            'dlxType' => $dlxType,
            'dlq' => $dlq,
            'messageTtl' => $messageTtl
        ];
        $this->rabbitmqService->createExchangeQueueAndBind($queue, $payload, true);

        /** Set Config to publish the event message */
        config([
            'queue.connections.rabbitmq.options.exchange.name' => $exchange,
            'queue.connections.rabbitmq.options.queue.exchange' => $exchange,
            'queue.connections.rabbitmq.options.exchange.type' => $type,
            'queue.connections.rabbitmq.options.queue.exchange_type' => $type,
            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => $binding,
        ]);
    }

    public function handle($event)
    {

    }
}
