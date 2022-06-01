<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Result;
use App\Services\BatchService;
use App\Services\CommonServices\CodeGeneratorService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class BatchController
 * @package App\Http\Controllers
 */
class BatchController extends Controller
{
    /**
     * @var BatchService
     */
    public BatchService $batchService;

    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * BatcheController constructor.
     * @param BatchService $batchService
     */
    public function __construct(BatchService $batchService)
    {
        $this->batchService = $batchService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->batchService->filterValidator($request)->validate();
        $response = $this->batchService->getBatchList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function getCourseBatches(Request $request): JsonResponse
    {
        $filter = $this->batchService->filterValidator($request)->validate();

        $response = $this->batchService->getBatchList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param int $fourIrInitiativeId
     * @return JsonResponse
     */
    public function getBatchesByFourIrInitiativeId(int $fourIrInitiativeId): JsonResponse
    {
        $response = $this->batchService->getFourIrBatchList($fourIrInitiativeId, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     *  * Display the specified resource
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $data = $this->batchService->getBatch($id);

        $response = formatSuccessResponse($data, $this->startTime, "Batch Fetch successfully.");

        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws RequestException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->batchService->validator($request)->validate();
        $validatedData['code'] = CodeGeneratorService::getBatchCode($validatedData['course_id']);
        DB::beginTransaction();
        try {
            $data = $this->batchService->store($validatedData);
            $this->batchService->createCalenderEventForBatch($data->toArray());

            $response = formatSuccessResponse($data, $this->startTime, "Batch added successfully.");

            DB::commit();

            return Response::json($response, ResponseAlias::HTTP_CREATED);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws RequestException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);

        $validated = $this->batchService->validator($request)->validate();
        DB::beginTransaction();
        try {
            $data = $this->batchService->update($batch, $validated);
            $this->batchService->updateCalenderEventOnBatchUpdate($data->toArray());

            $response = formatSuccessResponse($data, $this->startTime, "Batch update successfully.");

            DB::commit();

            return Response::json($response, ResponseAlias::HTTP_OK);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);

        DB::beginTransaction();

        try {
            $this->batchService->destroy($batch);
            $this->batchService->destroyCalenderEventByBatchId($id);

            $response = formatSuccessResponse(null, $this->startTime, "Batch Delete successfully!");

            DB::commit();

            return Response::json($response, ResponseAlias::HTTP_OK);
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function assignTrainerToBatch(Request $request, int $id): JsonResponse
    {
        $validated = $this->batchService->trainerValidator($request)->validated();
        $batch = Batch::findOrFail($id);
        $validated['trainerIds'] = !empty($validated['trainerIds']) ? $validated['trainerIds'] : [];
        $batch = $this->batchService->assignTrainer($batch, $validated['trainerIds']);

        $response = formatSuccessResponse($batch->trainers()->get(), $this->startTime, "Trainer added to batch successfully!");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->batchService->getBatchTrashList($request, $this->startTime);

        return Response::json($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $batch = Batch::onlyTrashed()->findOrFail($id);
        $this->batchService->restore($batch);

        $response = formatSuccessResponse(null, $this->startTime, "Batch restored successfully!");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $institute = Batch::onlyTrashed()->findOrFail($id);
        $this->batchService->forceDelete($institute);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Batch permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getBatchesByCourseId(Request $request, $id): JsonResponse
    {

        $response = $this->batchService->batchesWithTrainingCenters($request, $id, $this->startTime);

        return Response::json($response);
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getExamsByBatchId(Request $request, $id): JsonResponse
    {
        $data = $this->batchService->getExamListByBatch($request, $id);

        $response = formatSuccessResponse($data, $this->startTime, "Exams Fetch Successfully!");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function getYouthExamListByBatch(Request $request, $id): JsonResponse
    {
        $data = $this->batchService->getYouthExamListByBatch($request, $id);

        $response = formatSuccessResponse($data, $this->startTime, "Fetch Successfully!");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function getPublicYouthExamListByBatch(Request $request, $id): JsonResponse
    {
        $data = $this->batchService->getYouthExamListByBatch($request, $id);

        $response = formatSuccessResponse($data, $this->startTime, "Fetch Successfully!");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getPublicBatchesByCourseId(Request $request, $id): JsonResponse
    {

        $response = $this->batchService->batchesWithTrainingCenters($request, $id, $this->startTime, true);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function assignExamToBatch(Request $request, int $id): JsonResponse
    {
        $batch = Batch::findOrFail($id);
        $validatedData = $this->batchService->examTypeValidator($request)->validate();
        $validatedData['exam_type_ids'] = !empty($validatedData['exam_type_ids']) ? $validatedData['exam_type_ids'] : [];
        $this->batchService->assignExamToBatch($batch, $validatedData['exam_type_ids']);

        $response = formatSuccessResponse(null, $this->startTime, "Exams assigned to batch successfully");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function assignCertificateTemplateToBatch(Request $request, int $id): JsonResponse
    {

        $batch = Batch::findOrFail($id);
        $validatedData = $this->batchService->batchCertificateTemplateValidator($request)->validate();
        $validatedData['certificate_template_ids'] = !empty($validatedData['certificate_template_ids']) ? $validatedData['certificate_template_ids'] : [];
        $this->batchService->assignBatchCertificateTemplateIds($batch, $validatedData['certificate_template_ids']);

        $response = formatSuccessResponse(null, $this->startTime, "Certificate Template assigned to batch successfully");

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function processBatchResult(int $id): JsonResponse
    {
        $this->authorize('create', Result::class);

        $response = $this->batchService->processResult($id, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getBatchExamResults($id): JsonResponse
    {
        $this->authorize('viewAny', Result::class);

        $data = $this->batchService->getResultsByBatch($id);

        $response = formatSuccessResponse($data, $this->startTime, "Result Fetch Successfully");

        return Response::json($response);
    }


    /**
     * @param $resultId
     * @return JsonResponse
     */
    public function getBatchExamResultSummaries(int $resultId): JsonResponse
    {
        $data = $this->batchService->getResultSummariesByResult($resultId);

        $response = formatSuccessResponse($data, $this->startTime, "Result summaries Fetch Successfully");

        return Response::json($response);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     * @throws Throwable
     */
    public function publishBatchExamsResult(Request $request, int $id): JsonResponse
    {
        $this->authorize('create', Result::class);
        $batch = Batch::findOrFail($id);

        $validatedData = $this->batchService->resultPublishValidator($request)->validate();

        $isBatchAllYouthMarkUpdateDone = $this->batchService->isBatchAllYouthMarkUpdatedDone($id);

        if($batch->result_published_at){
            return Response::json(formatErrorResponse(["error_code" => "batch_result_already_published"], $this->startTime, "Batch Result Already Published!"));

        }
        if(!$isBatchAllYouthMarkUpdateDone){
            return Response::json(formatErrorResponse(["error_code" => "mark_update_not_complete"], $this->startTime, "All youth mark update not completed!"));
        }

        DB::beginTransaction();
        try {
            $this->batchService->publishExamResult($validatedData,$batch, $this->startTime);

            $message = $validatedData['is_published'] == Result::RESULT_PUBLISHED ? "Result published successfully" : "Result unpublished successfully";

            $response = formatSuccessResponse(null, $this->startTime, $message);

        }catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }


        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicYouthExamResultByBatch(Request $request, $id): JsonResponse
    {
        $validatedData = $this->batchService->youthExamResultValidator($request)->validate();

        $data = $this->batchService->getYouthExamResultByBatch($validatedData, $id);

        $response = formatSuccessResponse($data, $this->startTime, "Result Fetch Successfully");

        return Response::json($response);
    }

}
