<?php

namespace App\Listeners\RplApplication;

use App\Models\BaseModel;
use App\Models\RplApplication;
use App\Services\RabbitMQService;
use App\Traits\Scopes\SagaStatusGlobalScope;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;

class RplApplicationSuccessYouthToInstituteListener implements ShouldQueue
{
    private RabbitMQService $rabbitMQService;

    /**
     * @param RabbitMQService $rabbitMQService
     */
    public function __construct(RabbitMQService $rabbitMQService)
    {
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
        try {
            $this->rabbitMQService->receiveEventSuccessfully(
                BaseModel::SAGA_YOUTH_SERVICE,
                BaseModel::SAGA_INSTITUTE_SERVICE,
                get_class($this),
                json_encode($event)
            );

            $alreadyConsumed = $this->rabbitMQService->checkEventAlreadyConsumed();

            if (!$alreadyConsumed) {

                /** @var RplApplication $rplApplication */
                $rplApplication = RplApplication::withoutGlobalScope(SagaStatusGlobalScope::class)->findOrFail($data['rpl_application_id']);
                $rplApplication->saga_status = BaseModel::SAGA_STATUS_COMMIT;
                $rplApplication->save();
            }

            /**
             * Store the event as a Success event into Database.
             * If this event already previously consumed then again store it to saga_success_table to identify that this event again successfully consumed.
             */
            $this->rabbitMQService->sagaSuccessEvent(
                BaseModel::SAGA_YOUTH_SERVICE,
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
