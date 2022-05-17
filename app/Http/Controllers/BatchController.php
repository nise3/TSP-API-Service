<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\BatchService;
use App\Services\CommonServices\CodeGeneratorService;
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
//        dd($request);
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
     * @param int $id
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

        $response = [
            "data" => $data ?: [],
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now())
            ]
        ];

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

            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Batch added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
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
            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Batch update successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
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
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Batch Delete successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
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
        $response = [
            'data' => $batch->trainers()->get(),
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "trainer added to batch successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->batchService->getBatchTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $batch = Batch::onlyTrashed()->findOrFail($id);
        $this->batchService->restore($batch);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "batch restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @throws Throwable
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
        $data = $this->batchService->getExamListByBatch($id);
        $response = [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now())
            ]
        ];

        return Response::json($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getPublicBatchesByCourseId(Request $request, $id): JsonResponse
    {

        $response = $this->batchService->batchesWithTrainingCenters($request, $id, $this->startTime, true);

        return Response::json($response);
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
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "exams assigned to batch successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function processBatchResult(Request $request): JsonResponse
    {
        $validatedData = $this->batchService->resultProcessingValidator($request)->validate();
        $processResult = $this->batchService->processResult($validatedData);
//        $response = [
//            'data' => $batch->exams()->get(),
//            '_response_status' => [
//                "success" => true,
//                "code" => ResponseAlias::HTTP_OK,
//                "message" => "exams assigned to batch successfully",
//                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
//            ]
//        ];
//        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
