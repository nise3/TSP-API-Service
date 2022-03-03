<?php

namespace App\Http\Controllers;

use App\Models\RplOccupation;
use App\Models\RtoBatch;
use App\Services\RtoBatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RtoBatchController extends Controller
{
    /**
     * @var RtoBatchService
     */
    public RtoBatchService $rtoBatchService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param RtoBatchService $rtoBatchService
     */

    public function __construct(RtoBatchService $rtoBatchService)
    {
        $this->rtoBatchService = $rtoBatchService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getList(Request $request)
    {
        $this->authorize('viewAny', RtoBatch::class);
        $filter = $this->rtoBatchService->filterValidator($request)->validate();

        $response = $this->rtoBatchService->getRtoBatchList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rtoBatchService->filterValidator($request)->validate();

        $response = $this->rtoBatchService->getRtoBatchList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function read(Request $request, int $id)
    {
        $rtoBatch = $this->rtoBatchService->getOneRtoBatch($id);
        $this->authorize('view', $rtoBatch);

        $response = [
            "data" => $rtoBatch,
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
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', RtoBatch::class);
        $validated = $this->rtoBatchService->validator($request)->validate();
        $rtoBatch = $this->rtoBatchService->store($validated);

        $response = [
            'data' => $rtoBatch,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RPL Occupation added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id)
    {
        $rtoBatch = RtoBatch::findOrFail($id);

        $this->authorize('update', RtoBatch::class);

        $validated = $this->rtoBatchService->validator($request, $id)->validate();
        $data = $this->rtoBatchService->update($rtoBatch, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RPL Occupation updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
            return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\RtoBatch $rtoBatch
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id)
    {
        $rtoBatch = RplOccupation::findOrFail($id);

        $this->authorize('delete', $rtoBatch);

        DB::beginTransaction();
        try {
            $this->rtoBatchService->destroy($rtoBatch);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RPL Occupation deleted successfully.",
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
