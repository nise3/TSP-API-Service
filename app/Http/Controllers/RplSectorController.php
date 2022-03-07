<?php

namespace App\Http\Controllers;

use App\Models\RplSector;
use App\Services\RplSectorService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplSectorController extends Controller
{
    /**
     * @var RplSectorService
     */
    public RplSectorService $rplSectorService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplSectorController constructor.
     * @param RplSectorService $rplSectorService
     */

    public function __construct(RplSectorService $rplSectorService)
    {
        $this->rplSectorService = $rplSectorService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RplSector::class);

        $filter = $this->rplSectorService->filterValidator($request)->validate();

        $response = $this->rplSectorService->getRplSectorList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rplSectorService->filterValidator($request)->validate();

        $response = $this->rplSectorService->getRplSectorList($filter, $this->startTime,true);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * * Display the specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $rto = $this->rplSectorService->getOneRplSector($id);
        $this->authorize('view', $rto);

        $response = [
            "data" => $rto,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', RplSector::class);

        $validated = $this->rplSectorService->validator($request)->validate();
        $rplSector = $this->rplSectorService->store($validated);

        $response = [
            'data' => $rplSector,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RPL Sector added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rplSector = RplSector::findOrFail($id);

        $this->authorize('update', $rplSector);

        $validated = $this->rplSectorService->validator($request, $id)->validate();
        $data = $this->rplSectorService->update($rplSector, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RPL Sector updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $rplSector = RplSector::findOrFail($id);

        $this->authorize('delete', $rplSector);

        DB::beginTransaction();
        try {
            $this->rplSectorService->destroy($rplSector);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RPL Sector deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
