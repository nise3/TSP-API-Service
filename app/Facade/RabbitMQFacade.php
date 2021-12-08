<?php

namespace App\Facade;

use App\Services\RabbitMQService;
use Illuminate\Support\Facades\Facade;
use phpDocumentor\Reflection\Types\Boolean;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

/**
 * Class RabbitMQFacade
 * @package App\Facade
 * @method static bool publishEvent(RabbitMQConnector $connector, RabbitMQService $rabbitmqService, string $exchange, string $queue, Boolean $retry)
 *
 * @see \App\Helpers\Classes\RabbitMQ
 */
class RabbitMQFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'rabbit_mq';
    }
}
