<?php

namespace App\Listeners\CourseEnrollment;

use App\Models\BaseModel;
use App\Traits\Scopes\SagaStatusGlobalScope;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Throwable;
use PDOException;
use App\Services\RabbitMQService;
use App\Models\CourseEnrollment;

class CourseEnrollmentRollbackToInstituteListener
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

    /**
     * @param $event
     * @return void
     * @throws Exception
     */
    public function handle($event)
    {
        $eventData = json_decode(json_encode($event), true);
        $data = $eventData['data'] ?? [];
        $publisher = $data ? $data['publisher_service'] ?? "" : "";
        try {
            $this->rabbitMQService->receiveEventSuccessfully(
                $publisher,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($event)
            );

            $alreadyConsumed = $this->rabbitMQService->checkEventAlreadyConsumed();
            if (!$alreadyConsumed) {
                /** @var CourseEnrollment $courseEnrollment */
                $courseEnrollment = CourseEnrollment::find($data['enrollment_id'])->withoutGlobalScope(SagaStatusGlobalScope::class);
                $courseEnrollment->saga_status = BaseModel::SAGA_STATUS_ROLLBACK;
                $courseEnrollment->deleted_at = $this->currentTime;
                $courseEnrollment->save();
            }

            /**
             * Store the event as a Success event into Database.
             * If this event already previously consumed then again store it to saga_success_table to identify that this event again successfully consumed.
             */
            $this->rabbitMQService->sagaSuccessEvent(
                $publisher,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($data)
            );
        } catch (Throwable $e) {
            if ($e instanceof QueryException && $e->getCode() == BaseModel::DATABASE_CONNECTION_ERROR_CODE) {
                /** Technical Recoverable Error Occurred. RETRY mechanism with DLX-DLQ apply now by sending a rejection */
                throw new PDOException("Database Connectivity Error");
            } else {
                /** Technical Non-recoverable Error "OR" Business Rule violation Error Occurred */
                /** Store the event as an Error event into Database */
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
}
