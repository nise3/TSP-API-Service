<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],
        \App\Events\BatchCreated::class => [
            \App\Listeners\BatchCreatedListener::class,
        ],
        \App\Events\CourseEnrollmentEvent::class => [
            \App\Listeners\CourseEnrollmentListener::class,
        ],
    ];
}
