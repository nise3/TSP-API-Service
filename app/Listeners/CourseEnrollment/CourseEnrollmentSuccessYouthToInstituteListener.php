<?php

namespace App\Listeners\CourseEnrollment;

use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use Exception;
use App\Services\RabbitMQService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CourseEnrollmentSuccessYouthToInstituteListener implements ShouldQueue
{
    private RabbitMQService $rabbitMQService;

    /**
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService){
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * @param $event
     * @return void
     */
    public function handle($event){
        $this->rabbitMQService->receiveEventSuccessfully(
            BaseModel::SAGA_YOUTH_SERVICE,
            BaseModel::SAGA_INSTITUTE_SERVICE,
            get_class($this),
            json_encode($event)
        );
        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'] ?? [];
        try {
            /** @var CourseEnrollment $courseEnrollment */
            $courseEnrollment = CourseEnrollment::find($data['enrollment_id']);
            $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_COMMIT;
            $courseEnrollment->save();

            $this->rabbitMQService->sagaSuccessEvent(
                BaseModel::SAGA_YOUTH_SERVICE,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data)
            );
        } catch (Exception $e){
            $this->rabbitMQService->sagaErrorEvent(
                BaseModel::SAGA_YOUTH_SERVICE,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data),
                $e,
            );
        }
    }
}
