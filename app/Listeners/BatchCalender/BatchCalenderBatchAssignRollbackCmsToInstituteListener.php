<?php

namespace App\Listeners\BatchCalender;

use App\Facade\RabbitMQFacade;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\CourseEnrollment;
use App\Services\RabbitMQService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Connectors\RabbitMQConnector;

class BatchCalenderBatchAssignRollbackCmsToInstituteListener implements ShouldQueue
{
    private Carbon $currentTime;
    private RabbitMQService $rabbitMQService;

    /**
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->currentTime = Carbon::now();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle($event)
    {

        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'] ?? [];
        $publisher = $data ? $data['publisher_service'] ?? "" : "";
        try {
            /** @var CourseEnrollment $courseEnrollment */
            $courseEnrollment = CourseEnrollment::find($data['enrollment_id']);
            $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_ROLLBACK;
            $courseEnrollment->batch_id = null;
            $courseEnrollment->deleted_at = $this->currentTime;
            $courseEnrollment->save();


            $batch = Batch::find($data['batch_id']);
            $batch->avilable_seats += 1;

            $this->rabbitMQService->sagaSuccessEvent(
                $publisher,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data)
            );
        } catch (Exception $e) {
            $this->rabbitMQService->sagaErrorEvent(
                $publisher,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data),
                $e,
            );
        }

    }
}
