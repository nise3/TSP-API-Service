<?php

namespace App\Listeners;

use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use Illuminate\Contracts\Queue\ShouldQueue;

class CourseEnrollmentSuccessYouthToInstituteListener implements ShouldQueue
{
    public function __construct(){

    }

    /**
     * @param $event
     * @return void
     */
    public function handle($event){
        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'];

        /** @var CourseEnrollment $courseEnrollment */
        $courseEnrollment = CourseEnrollment::find($data['id']);
        $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_COMMIT;
        $courseEnrollment->save();
    }
}
