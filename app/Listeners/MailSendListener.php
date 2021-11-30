<?php

namespace App\Listeners;

use App\Services\RabbitMQService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

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
     * @return void
     * @throws Exception
     */
    private const EXCHANGE = "mail.sms.x";
    private const EXCHANGE_TYPE = "topic";
    private const EXCHANGE_QUEUE = "mail.q";
    private const EXCHANGE_BINDING_KEY = "mail";

    private const EXCHANGE_DURABLE = true;
    private const EXCHANGE_AUTO_DELETE = false;

    private const ALTER_EXCHANGE = "mail.sms.alternate.x";
    private const ALTER_EXCHANGE_TYPE = "fanout";
    private const ALTER_EXCHANGE_QUEUE = "mail.sms.alternate.q";

    private const DLX = "mail.sms.dlx";
    private const DLX_TYPE = "fanout";
    private const DLX_DL_QUEUE = "mail.sms.dlq";
    private const DLX_X_MESSAGE_TTL = 120000;


    /**
     * @throws AMQPProtocolChannelException
     * @throws Exception
     */
    private function publishEvent(): void
    {
        $config = config('queue.connections.rabbitmq');
        $queue = $this->connector->connect($config);

        /** Exchange Queue related variables */
        $exchange = self::EXCHANGE;
        $type = self::EXCHANGE_TYPE;
        $durable = self::EXCHANGE_DURABLE;
        $autoDelete = self::EXCHANGE_AUTO_DELETE;
        $queueName = self::EXCHANGE_QUEUE;
        $binding = self::EXCHANGE_BINDING_KEY;

        /** Alternate Exchange related variables */
        $alternateExchange = self::ALTER_EXCHANGE;
        $alternateExchangeType = self::ALTER_EXCHANGE_TYPE;
        $alternateQueue = self::ALTER_EXCHANGE_QUEUE;

        $exchangeArguments = [
            'alternate-exchange' => $alternateExchange
        ];

        /** DlX-DLQ related variables */
        $dlx = self::DLX;
        $dlxType = self::DLX_TYPE;
        $dlq = self::DLX_DL_QUEUE;
        $messageTtl = self::DLX_X_MESSAGE_TTL;

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
