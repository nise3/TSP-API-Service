<?php

namespace App\Http\Controllers;

use App\Models\YouthAssessment;
use App\Models\RplOccupation;
use App\Services\YouthAssessmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class YouthAssessmentController extends Controller
{
    /**
     * @var YouthAssessmentService
     */
    public YouthAssessmentService $youthAssessmentService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param YouthAssessmentService $youthAssessmentService
     */

    public function __construct(YouthAssessmentService $youthAssessmentService)
    {
        $this->youthAssessmentService = $youthAssessmentService;
        $this->startTime = Carbon::now();
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $this->authorize('viewAny', YouthAssessment::class);
        $filter = $this->youthAssessmentService->filterValidator($request)->validate();

        $response = $this->youthAssessmentService->getYouthAssessmentList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->youthAssessmentService->filterValidator($request)->validate();

        $response = $this->youthAssessmentService->getYouthAssessmentList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function read(Request $request, int $id)
    {
        $youthAssessment = $this->youthAssessmentService->getOneYouthAssessment($id);
        $this->authorize('view', $youthAssessment);

        $response = [
            "data" => $youthAssessment,
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
     */
    public function store(Request $request)
    {
        // $this->authorize('create', YouthAssessment::class); // not needed
        $validated = $this->youthAssessmentService->validator($request)->validate();
        $youthAssessment = $this->youthAssessmentService->store($validated);

        $response = [
            'data' => $youthAssessment,
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
     * @param Request $request
     * @param  int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $youthAssessment = YouthAssessment::findOrFail($id);

        $this->authorize('update', YouthAssessment::class);

        $validated = $this->youthAssessmentService->validator($request, $id)->validate();
        $data = $this->youthAssessmentService->update($youthAssessment, $validated);
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
     * @param \App\Models\YouthAssessment $youthAssessment
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id)
    {
        $youthAssessment = YouthAssessment::findOrFail($id);

        $this->authorize('delete', $youthAssessment);

        DB::beginTransaction();
        try {
            $this->youthAssessmentService->destroy($youthAssessment);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Youth Assessment deleted successfully.",
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
