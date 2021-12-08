<?php

namespace App\Listeners\CourseEnrollment;

use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use Illuminate\Support\Carbon;

class CourseEnrollmentRollbackToInstituteListener
{
    private \Carbon\Carbon|Carbon $currentTime;

    public function __construct(){
        $this->currentTime = Carbon::now();
    }

    /**
     * @param $event
     * @return void
     */
    public function handle($event){
        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'];

        /** @var CourseEnrollment $courseEnrollment */
        $courseEnrollment = CourseEnrollment::find($data['enrollment_id']);
        $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_ROLLBACK;
        $courseEnrollment->deleted_at = $this->currentTime;
        $courseEnrollment->save();
    }
}
