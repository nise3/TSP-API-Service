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
            'queue.connections.rabbitmq.options.queue.exchange' => 'nise3.topic',
            'queue.connections.rabbitmq.options.exchange.name' => 'nise3.topic',

            'queue.connections.rabbitmq.queue' => '1.topic',

            'queue.connections.rabbitmq.options.queue.exchange_routing_key' => 'topic.one',

            'queue.connections.rabbitmq.options.queue.exchange_type' => 'topic',
        ]);

        /*dd(config('queue.connections.rabbitmq.options.queue.exchange'),
            config('queue.connections.rabbitmq.options.exchange.name'),
            config('queue.connections.rabbitmq.queue'),
            config('queue.connections.rabbitmq.options.queue.exchange_routing_key'),
            config('queue.connections.rabbitmq.options.queue.exchange_type'));*/
    }

    public function handle($event)
    {

    }
}
