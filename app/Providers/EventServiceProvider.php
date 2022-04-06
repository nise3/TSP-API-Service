<?php

namespace App\Providers;

use App\Events\RplApplication\RplApplicationEvent;
use App\Listeners\RplApplication\RplApplicationInstituteToYouthListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\CourseEnrollment\CourseEnrollmentEvent::class => [
            \App\Listeners\CourseEnrollment\CourseEnrollmentInstituteToYouthListener::class,
        ],
        RplApplicationEvent::class => [
            RplApplicationInstituteToYouthListener::class
        ],
        \App\Events\MailSendEvent::class => [
            \App\Listeners\MailSendListener::class
        ],
        \App\Events\SmsSendEvent::class => [
            \App\Listeners\SmsSendListener::class
        ],
        \App\Events\BatchCalender\BatchCalenderYouthBatchAssignEvent::class => [
            \App\Listeners\BatchCalender\BatchCalenderYouthBatchAssignInstituteToCmsListener::class
        ],
    ];
}
