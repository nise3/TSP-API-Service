<?php

namespace App\Http\Controllers;

use App\Models\RplLevel;
use App\Services\RplLevelService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplLevelController extends Controller
{
    /**
     * @var RplLevelService
     */
    public RplLevelService $rplLevelService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplLevelController constructor.
     * @param RplLevelService $rplLevelService
     */

    public function __construct(RplLevelService $rplLevelService)
    {
        $this->rplLevelService = $rplLevelService;
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
        $this->authorize('viewAny', RplLevel::class);

        $filter = $this->rplLevelService->filterValidator($request)->validate();

        $response = $this->rplLevelService->getRplLevelList($filter, $this->startTime);
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
        $filter = $this->rplLevelService->filterValidator($request)->validate();

        $response = $this->rplLevelService->getRplLevelList($filter, $this->startTime,true);
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
        $rplLevel = $this->rplLevelService->getOneRplLevel($id);
        $this->authorize('view', $rplLevel);

        $response = [
            "data" => $rplLevel,
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
        $this->authorize('create', RplLevel::class);

        $validated = $this->rplLevelService->validator($request)->validate();
        $rplLevel = $this->rplLevelService->store($validated);

        $response = [
            'data' => $rplLevel,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RPL Level added successfully",
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
        $rplLevel = RplLevel::findOrFail($id);

        $this->authorize('update', $rplLevel);

        $validated = $this->rplLevelService->validator($request, $id)->validate();
        $data = $this->rplLevelService->update($rplLevel, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RPL Level updated successfully.",
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
        $rplLevel = RplLevel::findOrFail($id);

        $this->authorize('delete', $rplLevel);

        DB::beginTransaction();
        try {
            $this->rplLevelService->destroy($rplLevel);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RPL Level deleted successfully.",
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
