<?php

namespace App\Facade;

/**
 * Class RabbitMQ
 * @package App\Facade
 */
class RabbitMQ
{
    protected static function getFacadeAccessor(): string
    {
        return 'rabbit_mq';
    }
}
