<?php

namespace App\Listeners\BatchCalender;

use App\Facade\RabbitMQFacade;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\CourseEnrollment;
use App\Services\RabbitMQService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class BatchCalenderBatchAssignSuccessCmsToInstituteListener implements ShouldQueue
{
    private RabbitMQService $rabbitMQService;

    /**
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle($event)
    {
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
        } catch (Exception $e) {
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
