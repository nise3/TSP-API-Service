<?php

namespace App\Listeners\BatchCalender;

use App\Models\BaseModel;
use App\Models\CourseEnrollment;
use App\Services\RabbitMQService;
use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDOException;

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
            $this->rabbitMQService->receiveEventSuccessfully(
                BaseModel::SAGA_CMS_SERVICE,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($event)
            );

            $alreadyConsumed = $this->rabbitMQService->checkEventAlreadyConsumed();
            if (!$alreadyConsumed) {
                /** @var CourseEnrollment $courseEnrollment */
                $courseEnrollment = CourseEnrollment::find($data['enrollment_id']);
                $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_COMMIT;
                $courseEnrollment->save();

                /** Store the event as a Success event into Database */
                $this->rabbitMQService->sagaSuccessEvent(
                    BaseModel::SAGA_CMS_SERVICE,
                    BaseModel::SAGA_INSTITUTE_SERVICE,
                    get_class($this),
                    json_encode($data)
                );
            }
        } catch (Throwable $e) {
            if ($e instanceof QueryException && $e->getCode() == BaseModel::DATABASE_CONNECTION_ERROR_CODE) {
                /** Technical Recoverable Error Occurred. RETRY mechanism with DLX-DLQ apply now by sending a rejection */
                throw new PDOException("Database Connectivity Error");
            } else {
                /** Technical Non-recoverable Error "OR" Business Rule violation Error Occurred */
                /** Store the event as an Error event into Database */
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
}
