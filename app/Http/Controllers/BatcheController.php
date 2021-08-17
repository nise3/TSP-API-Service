<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\Batche;
use App\Services\BatcheService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Class BatcheController
 * @package App\Http\Controllers
 */
class BatcheController extends Controller
{
    /**
     * @var BatcheService
     */
    public BatcheService $batcheService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon $startTime;

    /**
     * BatcheController constructor.
     * @param BatcheService $batcheService
     */
    public function __construct(BatcheService $batcheService)
    {
        $this->batcheService = $batcheService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->batcheService->getCourseConfigList($request,  $this->startTime);
        } catch (Throwable $e) {
            return  $e;
        }
        return Response::json($response);
    }

    /**
     * @param int $id
     *  * Display the specified resource
     *  @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->batcheService->getOneCourseConfig($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->batcheService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $data = $this->batcheService->store($validatedData);
            DB::commit();
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Batch added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $courseConfig = Batche::findOrFail($id);
        $validated = $this->batcheService->validator($request)->validate();
        try {
            $data = $this->batcheService->update($courseConfig, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Batch update successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $courseConfig = Batche::findOrFail($id);

        try {
            $this->batcheService->destroy($courseConfig);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Batch Delete successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, JsonResponse::HTTP_OK);
    }
}
