<?php

namespace App\Facade;

use App\Services\RabbitMQService;
use phpDocumentor\Reflection\Types\Boolean;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

/**
 * Class RabbitMQFacade
 * @package App\Facade
 * @method static bool publishEvent(RabbitMQConnector $connector, RabbitMQService $rabbitmqService, string $exchange, string $queue, Boolean $retry)
 */
class RabbitMQFacade
{
    protected static function getFacadeAccessor(): string
    {
        return 'rabbit_mq';
    }
}
