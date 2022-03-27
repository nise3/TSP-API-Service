<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\RplOccupation;
use App\Services\AssessmentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class AssessmentController extends Controller
{
    /**
     * @var AssessmentService
     */
    public AssessmentService $assessmentService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param AssessmentService $assessmentService
     */

    public function __construct(AssessmentService $assessmentService)
    {
        $this->assessmentService = $assessmentService;
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
        $this->authorize('viewAny', Assessment::class);
        $filter = $this->assessmentService->filterValidator($request)->validate();

        $response = $this->assessmentService->getAssessmentList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->assessmentService->filterValidator($request)->validate();

        $response = $this->assessmentService->getAssessmentList($filter, $this->startTime);
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
        $assessment = $this->assessmentService->getOneAssessment($id);
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
        $this->authorize('create', Assessment::class);
        $validated = $this->assessmentService->validator($request)->validate();
        $assessment = $this->assessmentService->store($validated);

        $response = [
            'data' => $assessment,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Assessment added successfully",
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
        $assessment = Assessment::findOrFail($id);

        $this->authorize('update', $assessment);

        $validated = $this->assessmentService->validator($request, $id)->validate();
        $data = $this->assessmentService->update($assessment, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Assessment updated successfully.",
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
        $assessment = Assessment::findOrFail($id);

        $this->authorize('delete', $assessment);

        DB::beginTransaction();
        try {
            $this->assessmentService->destroy($assessment);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Assessment deleted successfully.",
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
