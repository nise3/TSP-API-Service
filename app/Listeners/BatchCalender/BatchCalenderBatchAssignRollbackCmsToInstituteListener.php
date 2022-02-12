<?php

namespace App\Listeners\BatchCalender;

use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\CourseEnrollment;
use App\Services\RabbitMQService;
use Illuminate\Support\Facades\DB;
use PDOException;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Database\QueryException;

class BatchCalenderBatchAssignRollbackCmsToInstituteListener implements ShouldQueue
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
                DB::beginTransaction();

                /** @var CourseEnrollment $courseEnrollment */
                $courseEnrollment = CourseEnrollment::find($data['enrollment_id']);
                $courseEnrollment->batch_id = $data['saga_previous_data']['batch_id'];
                $courseEnrollment->saga_status = $data['saga_previous_data']['saga_status'];
                $courseEnrollment->row_status = $data['saga_previous_data']['row_status'];
                $courseEnrollment->save();

                /** Return the previously booked seat */
                $batch = Batch::find($data['batch_id']);
                $batch->avilable_seats += 1;

                DB::commit();
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
            DB::rollBack();
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
