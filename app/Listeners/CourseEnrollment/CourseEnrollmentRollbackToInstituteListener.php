<?php

namespace App\Listeners\CourseEnrollment;

use App\Models\BaseModel;
use Exception;
use App\Services\RabbitMQService;
use App\Models\CourseEnrollment;
use Illuminate\Support\Carbon;

class CourseEnrollmentRollbackToInstituteListener
{
    private \Carbon\Carbon|Carbon $currentTime;
    private RabbitMQService $rabbitMQService;

    /**
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService){
        $this->currentTime = Carbon::now();
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * @param $event
     * @return void
     */
    public function handle($event){
        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'] ?? [];
        $publisher = $data ? $data['publisher_service'] ?? "" : "";
        try {
            /** @var CourseEnrollment $courseEnrollment */
            $courseEnrollment = CourseEnrollment::find($data['enrollment_id']);
            $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_ROLLBACK;
            $courseEnrollment->deleted_at = $this->currentTime;
            $courseEnrollment->save();

            $this->rabbitMQService->sagaSuccessEvent(
                $publisher,
                BaseModel::INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data)
            );
        } catch (Exception $e){
            $this->rabbitMQService->sagaErrorEvent(
                $publisher,
                BaseModel::INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data),
                $e,
            );
        }
    }
}
