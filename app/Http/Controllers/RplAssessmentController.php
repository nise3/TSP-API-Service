<?php

namespace App\Http\Controllers;

use App\Models\RplAssessment;
use App\Services\RplAssessmentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplAssessmentController extends Controller
{
    /**
     * @var RplAssessmentService
     */
    public RplAssessmentService $rplAssessmentService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param RplAssessmentService $rplAssessmentService
     */

    public function __construct(RplAssessmentService $rplAssessmentService)
    {
        $this->rplAssessmentService = $rplAssessmentService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException|ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RplAssessment::class);
        $filter = $this->rplAssessmentService->filterValidator($request)->validate();

        $response = $this->rplAssessmentService->getAssessmentList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rplAssessmentService->filterValidator($request)->validate();

        $response = $this->rplAssessmentService->getAssessmentList($filter, $this->startTime);
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
        $assessment = $this->rplAssessmentService->getOneAssessment($id);
        $this->authorize('view', $assessment);

        $response = [
            "data" => $assessment,
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
        $this->authorize('create', RplAssessment::class);
        $validated = $this->rplAssessmentService->validator($request)->validate();
        $assessment = $this->rplAssessmentService->store($validated);

        $response = [
            'data' => $assessment,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RplAssessment added successfully",
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
        $assessment = RplAssessment::findOrFail($id);

        $this->authorize('update', $assessment);

        $validated = $this->rplAssessmentService->validator($request, $id)->validate();
        $data = $this->rplAssessmentService->update($assessment, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RplAssessment updated successfully.",
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
        $assessment = RplAssessment::findOrFail($id);

        $this->authorize('delete', $assessment);

        DB::beginTransaction();
        try {
            $this->rplAssessmentService->destroy($assessment);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RplAssessment deleted successfully.",
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
