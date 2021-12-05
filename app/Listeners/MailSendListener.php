<?php

namespace App\Listeners;


use App\Services\CommonServices\RabbitMQService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
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
     * @return void
     * @throws Exception
     */

    /** Exchange related variables */
    private const EXCHANGE = "mail.sms.x";
    private const EXCHANGE_TYPE = "topic";
    private const EXCHANGE_QUEUE = "mail.q";
    private const EXCHANGE_BINDING_KEY = "mail";

    private const EXCHANGE_DURABLE = true;
    private const EXCHANGE_AUTO_DELETE = false;

    /** Alternate Exchange related variables */
    private const ALTER_EXCHANGE = "mail.sms.alternate.x";
    private const ALTER_EXCHANGE_TYPE = "fanout";
    private const ALTER_EXCHANGE_QUEUE = "mail.sms.alternate.q";
    private const ALTER_EXCHANGE_DURABLE = true;
    private const ALTER_EXCHANGE_AUTO_DELETE = false;

    /** DlX-DLQ related variables */
    private const DLX = "mail.sms.dlx";
    private const DLX_TYPE = "fanout";
    private const DLX_DL_QUEUE = "mail.sms.dlq";
    private const DLX_X_MESSAGE_TTL = 12000;


    /**
     * @return void
     * @throws Exception
     * @throws Exception
     * @throws AMQPProtocolChannelException
     */
    private function publishEvent(): void
    {
        /** Set Config to publish the event message */
        config([
            'queue.connections.rabbitmq.options.exchange.name' => self::EXCHANGE,
            'queue.connections.rabbitmq.options.queue.exchange' => self::EXCHANGE,
            'queue.connections.rabbitmq.options.exchange.type' => self::EXCHANGE_TYPE,
            'queue.connections.rabbitmq.options.queue.exchange_type' => self::EXCHANGE_TYPE,
            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => self::EXCHANGE_BINDING_KEY,
        ]);

        $config = config('queue.connections.rabbitmq');
        $queue = $this->connector->connect($config);

        $exchangeArguments = [
            'alternate-exchange' => self::ALTER_EXCHANGE
        ];

        /** Create Alternate Exchange, Queue and Bind Queue for Mail-SMS Exchange */
        $payload = [
            'exchange' => self::ALTER_EXCHANGE,
            'type' => self::ALTER_EXCHANGE_TYPE,
            'durable' => self::ALTER_EXCHANGE_DURABLE, /** DATA STORE IN CASE OF CONNECTION FAILURE */
            'autoDelete' => self::ALTER_EXCHANGE_AUTO_DELETE, /** IF TRUE= THE EXCHANGE IS DELETED IN CASE OF THERE IS NO BOUND QUEUE AND IF=FALSE THE EXCHANGE IS NOT DELETED  */
            'queueName' => self::ALTER_EXCHANGE_QUEUE,
            'binding' => ""
        ];
        $this->rabbitmqService->createExchangeQueueAndBind($queue, $payload, false);

        /** Create Exchange, Queue and Bind Queue with Retry by using DLX-DLQ for RETRY mechanism */
        $payload = [
            'exchange' => self::EXCHANGE,
            'type' => self::EXCHANGE_TYPE,
            'durable' => self::EXCHANGE_DURABLE, /** DATA STORE IN CASE OF CONNECTION FAILURE */
            'autoDelete' => self::EXCHANGE_AUTO_DELETE, /** IF TRUE= THE EXCHANGE IS DELETED IN CASE OF THERE IS NO BOUND QUEUE AND IF=FALSE THE EXCHANGE IS NOT DELETED  */
            'exchangeArguments' => $exchangeArguments,
            'queueName' => self::EXCHANGE_QUEUE,
            'binding' => self::EXCHANGE_BINDING_KEY,
            'dlx' => self::DLX,
            'dlxType' => self::DLX_TYPE,
            'dlq' => self::DLX_DL_QUEUE,
            'messageTtl' => self::DLX_X_MESSAGE_TTL
        ];
        $this->rabbitmqService->createExchangeQueueAndBind($queue, $payload, true);



        Log::info(config('queue.connections.rabbitmq.options.exchange.name'));
        Log::info(config('queue.connections.rabbitmq.options.queue.exchange'));
        Log::info(config('queue.connections.rabbitmq.options.exchange.type'));
        Log::info(config('queue.connections.rabbitmq.options.queue.exchange_type'));
        Log::info(config('queue.connections.rabbitmq.options.queue.exchange_routing_key'));
    }

    public function handle($event)
    {

    }
}
