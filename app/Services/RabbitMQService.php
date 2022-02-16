<?php

namespace App\Services;

use Exception;
use App\Models\BaseModel;
use App\Models\SagaErrorEvent;
use App\Models\SagaSuccessEvent;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpParser\Builder;
use VladimirYuldashev\LaravelQueueRabbitMQ\Helpers\RabbitMQ;
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
    public function createQueueAndBindWithoutRetry(RabbitMQQueue $queue, array $payload): void
    {
        /** Exchange Queue related variables */
        $exchange = $payload['exchange'];
        $queueName = $payload['queueName'];
        $binding = $payload['binding'] ?? "";
        $queueDurable = $payload['queueDurable'] ?? true;
        $queueAutoDelete = $payload['queueAutoDelete'] ?? false;
        $queueMode = $payload['queueMode'] ?? 'lazy';

        /** Create Queue Arguments */
        $queueArguments = [
            'x-queue-mode' => $queueMode
        ];

        /** Create Queue */
        if (!$queue->isQueueExists($queueName)) {
            $queue->declareQueue(
                $queueName, $queueDurable, $queueAutoDelete, $queueArguments
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
    public function createQueueAndBindWithRetry(RabbitMQQueue $queue, array $payload): void
    {
        /** Exchange Queue related variables */
        $exchange = $payload['exchange'];
        $queueName = $payload['queueName'];
        $binding = $payload['binding'] ?? "";
        $queueDurable = $payload['queueDurable'] ?? true;
        $queueAutoDelete = $payload['queueAutoDelete'] ?? false;
        $queueMode = $payload['queueMode'] ?? 'lazy';

        /** DLX-DLQ related variables */
        $dlx = $payload['dlx'];
        $dlxType = $payload['dlxType'];
        $dlxDurable = $payload['dlxDurable'] ?? true;
        $dlxAutoDelete = $payload['dlxAutoDelete'] ?? false;
        $dlq = $payload['dlq'];
        $dlqMessageTtl = $payload['messageTtl'];
        $dlqDurable = $payload['dlqDurable'] ?? true;
        $dlqAutoDelete = $payload['dlqAutoDelete'] ?? false;
        $dlqQueueMode = $payload['dlqQueueMode'] ?? 'lazy';

        /** Create DLQ Arguments */
        $dlqArguments = [
            'x-dead-letter-exchange' => $exchange,
            'x-message-ttl' => (int)$dlqMessageTtl,
            'x-queue-mode' => $dlqQueueMode
        ];

        /** Create Queue Arguments */
        $queueArguments = [
            'x-dead-letter-exchange' => $dlx,
            'x-queue-mode' => $queueMode
        ];

        /** Create DLX */
        if (!$queue->isExchangeExists($dlx)) {
            $queue->declareExchange(
                $dlx, $dlxType, $dlxDurable, $dlxAutoDelete
            );
        }
        /** Create DLQ */
        if (!$queue->isQueueExists($dlq)) {
            $queue->declareQueue(
                $dlq, $dlqDurable, $dlqAutoDelete, $dlqArguments
            );
        }
        /** Bind DLQ with DLX */
        $queue->bindQueue(
            $dlq, $dlx, $binding
        );

        /** Create Queue */
        if (!$queue->isQueueExists($queueName)) {
            $queue->declareQueue(
                $queueName, $queueDurable, $queueAutoDelete, $queueArguments
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
    public function createExchangeQueueAndBind(RabbitMQQueue $queue, array $payload, bool $retry = false): void
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

    /**
     * @return array
     */
    public function getRabbitMqMessage(): array
    {
        $rabbitMq = app(RabbitMQ::class);
        $rabbitMqJob = $rabbitMq->getRabbitMqJob();
        return json_decode(json_encode($rabbitMqJob->getRabbitMQMessage()), true);
    }

    /**
     * @return bool
     */
    public function checkEventAlreadyConsumed(): bool
    {
        $uuid = $this->getRabbitMqMessageUuid();

        /** @var SagaSuccessEvent $sagaEvent */
        $sagaEvent = SagaSuccessEvent::where('uuid', $uuid)->first();
        return (bool)$sagaEvent;
    }

    /**
     * @return string
     */
    public function getRabbitMqMessageUuid(): string
    {
        $message = $this->getRabbitMqMessage();
        $messageBody = $message['body'] ? json_decode($message['body'], true) : "";
        return $messageBody['uuid'] ?? "";
    }

    /**
     * @return string
     */
    public function getRabbitMqMessageExchange(): string
    {
        $message = $this->getRabbitMqMessage();
        $messageDeliveryInfo = $message['delivery_info'] ?? "";
        return $messageDeliveryInfo['exchange'] ?? "";
    }

    /**
     * @return string
     */
    public function getRabbitMqMessageRoutingKey(): string
    {
        $message = $this->getRabbitMqMessage();
        $messageDeliveryInfo = $message['delivery_info'] ?? "";
        return $messageDeliveryInfo['routing_key'] ?? "";
    }

    /**
     * @param String $publisher
     * @param String $consumer
     * @param String $listener
     * @param String $eventData
     * @param Exception|null $error
     * @return array
     */
    public function createSagaPayload(string $publisher, string $consumer, string $listener, string $eventData, \Throwable $error = null): array
    {
        $uuid = $this->getRabbitMqMessageUuid();
        $exchange = $this->getRabbitMqMessageExchange();
        $routingKey = $this->getRabbitMqMessageRoutingKey();
        $sagaPayload = [
            'uuid' => $uuid,
            'connection' => 'rabbitmq',
            'publisher' => $publisher,
            'listener' => $listener,
            'exchange' => $exchange,
            'routing_key' => $routingKey,
            'consumer' => $consumer,
            'event_data' => $eventData
        ];
        if ($error) {
            $sagaPayload['error_message'] = $error->getMessage();
        }
        return $sagaPayload;
    }

    /**
     * @param String $publisher
     * @param String $consumer
     * @param String $listener
     * @param String $eventData
     * @return void
     */
    public function sagaSuccessEvent(string $publisher, string $consumer, string $listener, string $eventData): void
    {
        $sagaPayload = $this->createSagaPayload($publisher, $consumer, $listener, $eventData);

        /** Remove the event from Error table if exist */
        $errorEvent = SagaErrorEvent::where('uuid', $sagaPayload['uuid'])->first();
        $errorEvent?->delete();

        /** Store the event as a Success event into Database */
        $sagaSuccessEvent = app(SagaSuccessEvent::class);
        $sagaSuccessEvent->fill($sagaPayload);
        $sagaSuccessEvent->save();

        /** Log in saga.log */
        Log::channel('saga')->info('########################################### SUCCESS to Event Consumed START ###########################################');
        Log::channel('saga')->info('Database Index ############# ' . $sagaSuccessEvent['id']);
        Log::channel('saga')->info('Event Transfer Info ######## ', $sagaSuccessEvent->toArray());
        Log::channel('saga')->info('Event Data ################# ', json_decode($eventData, true));
        Log::channel('saga')->info('############################################ SUCCESS to Event Consumed End ############################################');
    }

    /**
     * @param String $publisher
     * @param String $consumer
     * @param String $listener
     * @param String $eventData
     * @param Exception $error
     * @return void
     */
    public function sagaErrorEvent(string $publisher, string $consumer, string $listener, string $eventData, \Throwable $error)
    {
        $sagaPayload = $this->createSagaPayload($publisher, $consumer, $listener, $eventData, $error);

        /** Check weather the event already stored in Error Table or Not. If not then Store. */
        $errorEvent = SagaErrorEvent::where('uuid', $sagaPayload['uuid'])->first();
        if (!$errorEvent) {
            /** @var SagaErrorEvent|Builder $errorEvent */
            $errorEvent = app(SagaErrorEvent::class);
            $errorEvent->fill($sagaPayload);
            $errorEvent->save();
        }

        /** Log in saga.log */
        Log::channel('saga')->info('########################################### ERROR in Event Consumed Start ###########################################');
        Log::channel('saga')->info('Database Index ########### ' . $errorEvent['id']);
        Log::channel('saga')->info('Error Message ############ ' . $error->getMessage());
        Log::channel('saga')->info('Event Transfer Info ###### ', $errorEvent->toArray());
        Log::channel('saga')->info('Event Data ############### ', json_decode($eventData, true));
        Log::channel('saga')->info('Error Trac Trace ######### ' . $error->getTraceAsString());
        Log::channel('saga')->info('############################################ ERROR in Event Consumed End ############################################');
    }

    public function receiveEventSuccessfully(string $publisher, string $consumer, string $listener, string $eventData)
    {
        $sagaPayload = $this->createSagaPayload($publisher, $consumer, $listener, $eventData);

        /** Log in saga.log */
        Log::channel('saga')->info('########################################### Event received successfully Start ###########################################');
        Log::channel('saga')->info('Event Transfer Info ###### ', $sagaPayload);
        Log::channel('saga')->info('Event Data ############### ' . $eventData);
        Log::channel('saga')->info('############################################ Event received successfully End ############################################');
    }
}
