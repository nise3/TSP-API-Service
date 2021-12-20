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
        \App\Events\CourseEnrollmentEvent::class => [
            \App\Listeners\CourseEnrollmentListener::class,
        ],
        \App\Events\SmsSendEvent::class => [
            \App\Listeners\SmsSendListener::class
        ],
        \App\Events\MailSendEvent::class => [
            \App\Listeners\MailSendListener::class
        ]
    ];
}
