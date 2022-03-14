<?php

namespace App\Http\Controllers;

use App\Models\RplOccupation;
use App\Models\RtoBatch;
use App\Services\RtoBatchService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getList(Request $request): JsonResponse
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
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
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
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', RtoBatch::class);
        $authUser = Auth::user();

        if($authUser->isRtoUser()){
            $request->offsetSet('rto_id', $request->input('registered_training_organization_id'));
        }

        $validated = $this->rtoBatchService->validator($request)->validate();
        if(!empty($validated['institute_id'])){
            $validated['certification_status']= RtoBatch::CERTIFICATION_STATUS_SUBMITTED;
        }
        $rtoBatch = $this->rtoBatchService->store($validated);

        $response = [
            'data' => $rtoBatch,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RTO batch added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException|ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
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
                "message" => "RTO batch updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function assignAssessor(Request $request, int $id): JsonResponse
    {
        $youthAssessment = RtoBatch::findOrFail($id);

        $this->authorize('update', RtoBatch::class);

        $validated = $this->rtoBatchService->assignAssessorValidator($request)->validate();
        if(!empty($request['institute_id'])){
            $validated['certification_status']= RtoBatch::CERTIFICATION_STATUS_NOT_CERTIFIED;
        }
        $data = $this->rtoBatchService->update($youthAssessment, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Assessor assigned successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $rtoBatch = RtoBatch::findOrFail($id);

        $this->authorize('delete', $rtoBatch);

        DB::beginTransaction();
        try {
            $this->rtoBatchService->destroy($rtoBatch);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RTO batch deleted successfully.",
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
