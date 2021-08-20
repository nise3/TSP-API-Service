<?php

namespace App\Http\Controllers;

use App\Models\Institute;
use Exception;
use \Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Services\InstituteService;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class InstituteController extends Controller
{
    /**
     * @var InstituteService
     */
    public InstituteService $instituteService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * InstituteController constructor.
     * @param InstituteService $instituteService
     */
    public function __construct(InstituteService $instituteService)
    {
        $this->instituteService = $instituteService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->instituteService->getInstituteList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * * Display the specified resource
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->instituteService->getOneInstitute($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->instituteService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $data = $this->instituteService->store($validatedData);
            DB::commit();
            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Institute added successfully",
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
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);

        $validated = $this->instituteService->validator($request, $id)->validate();

        try {
            $data = $this->instituteService->update($institute, $validated);

            $response = [
                'data' => $data ?: [],
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Institute updated successfully.",
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
     *  * Remove the specified resource from storage.
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);
        try {
            $this->instituteService->destroy($institute);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Institute deleted successfully.",
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
