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
            'queue.connections.rabbitmq.options.queue.exchange' => config('nise3RabbitMq.exchanges.nise3Exchange.name'),
            'queue.connections.rabbitmq.options.exchange.name' => config('nise3RabbitMq.exchanges.nise3Exchange.name'),

            'queue.connections.rabbitmq.queue' => config('nise3RabbitMq.exchanges.nise3Exchange.queues.courseEnrollment.name'),

            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => config('nise3RabbitMq.exchanges.nise3Exchange.routingKey'),

            'queue.connections.rabbitmq.options.queue.exchange_type' => config('nise3RabbitMq.exchanges.nise3Exchange.type'),
        ]);
/*        dd(config('queue.connections.rabbitmq.options.queue.exchange'),
            config('queue.connections.rabbitmq.options.exchange.name'),
            config('queue.connections.rabbitmq.queue'),
            config('queue.connections.rabbitmq.options.queue.exchange_routing_key'),
            config('queue.connections.rabbitmq.options.queue.exchange_type'));*/
//        dd(config('nise3.is_dev_mode') ? config('nise3RabbitMq.host.local') : config('nise3RabbitMq.host.live'));
    }

    public function handle($event)
    {

    }
}
