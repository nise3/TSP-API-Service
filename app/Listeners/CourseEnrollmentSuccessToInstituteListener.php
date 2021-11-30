<?php

namespace App\Listeners;

use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class CourseEnrollmentSuccessToInstituteListener implements ShouldQueue
{
    public function __construct(){

    }

    /**
     * @param $event
     * @return void
     */
    public function handle($event){
        $data = json_decode(json_encode($event), true);

        Log::info("ttttttttttttttttttttttttttttttttt");
        Log::info(json_encode($data));
        Log::info($data['id']);

        /** @var CourseEnrollment $courseEnrollment */
        $courseEnrollment = CourseEnrollment::find($data['id']);
        $courseEnrollment['saga_status'] = BaseModel::SAGA_STATUS_COMMIT;
        $courseEnrollment->fill($data);
        $courseEnrollment->save();
    }
}
