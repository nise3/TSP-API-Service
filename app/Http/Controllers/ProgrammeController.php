<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Programme;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\ProgrammeService;

/**
 * Class ProgrammeController
 * @package App\Http\Controllers
 */
class ProgrammeController extends Controller
{
    /**
     * @var ProgrammeService
     */
    public ProgrammeService $programmeService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * ProgrammeController constructor.
     * @param ProgrammeService $programmeService
     */
    public function __construct(ProgrammeService $programmeService)
    {
        $this->programmeService = $programmeService;
        $this->startTime = Carbon::now();
    }

    /**
     * * * Display a listing of the resource.
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->programmeService->getProgrammeList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * * Display the specified resource
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->programmeService->getOneProgramme($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * Store a newly created resource in storage.
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->programmeService->validator($request)->validate();
        try {
            $data = $this->programmeService->store($validatedData);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Programme added successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $programme = Programme::findOrFail($id);
        $validated = $this->programmeService->validator($request, $id)->validate();
        try {
            $data = $this->programmeService->update($programme, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Programme updated successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  *   Remove the specified resource from storage.
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $programme = Programme::findOrFail($id);
        try {
            $this->programmeService->destroy($programme);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Programme deleted successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
