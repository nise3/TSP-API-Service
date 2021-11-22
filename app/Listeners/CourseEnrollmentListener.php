<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CourseEnrollmentListener implements ShouldQueue
{
    public string $connection = 'rabbitmq';

    public function __construct()
    {
        config([
            'queue.connections.rabbitmq.options.queue.exchange' => config('nise3RabbitMq.exchanges.courseEnrollmentExchange.name'),
            'queue.connections.rabbitmq.options.exchange.name' => config('nise3RabbitMq.exchanges.courseEnrollmentExchange.name'),

            'queue.connections.rabbitmq.queue' => config('nise3RabbitMq.exchanges.courseEnrollmentExchange.queues.courseEnrollmentQueue1.name'),

            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => config('nise3RabbitMq.exchanges.courseEnrollmentExchange.routingKey'),

            'queue.connections.rabbitmq.options.queue.exchange_type' => config('nise3RabbitMq.exchanges.courseEnrollmentExchange.type'),
        ]);
/*        dd(config('queue.connections.rabbitmq.options.queue.exchange'),
            config('queue.connections.rabbitmq.options.exchange.name'),
            config('queue.connections.rabbitmq.queue'),
            config('queue.connections.rabbitmq.options.queue.exchange_routing_key'),
            config('queue.connections.rabbitmq.options.queue.exchange_type'));*/
    }

    public function handle($event)
    {

    }
}
