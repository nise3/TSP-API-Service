<?php

namespace App\Http\Controllers;

use App\Models\YouthAssessment;
use App\Models\RplOccupation;
use App\Services\YouthAssessmentService;
use Illuminate\Auth\Access\AuthorizationException;
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
     * @throws AuthorizationException|ValidationException
     */
    public function getList(Request $request): JsonResponse
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
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
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
    public function store(Request $request): JsonResponse
    {
        // $this->authorize('create', YouthAssessment::class); // not needed for public
        $validated = $this->youthAssessmentService->validator($request)->validate();
        $assessmentId = $validated['assessment_id'];
        $youthAssessment = YouthAssessment::where('assessment_id',$assessmentId)->firstOrFail();
        $answers = $this->youthAssessmentService->answersValidator($request)->validate();
        $youthAssessment = $this->youthAssessmentService->store($youthAssessment,$validated);
        $youthAssessment = $this->youthAssessmentService->updateResult($youthAssessment, $answers);

        $response = [
            'data' => $youthAssessment,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Youth assessment added successfully",
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
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $youthAssessment = YouthAssessment::findOrFail($id);

        $this->authorize('update', $youthAssessment);

        $validated = $this->youthAssessmentService->validator($request, $id)->validate();
        $data = $this->youthAssessmentService->update($youthAssessment, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth assessment updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
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
    public function assignToBatch(Request $request, int $id): JsonResponse
    {
        $youthAssessment = YouthAssessment::findOrFail($id);

        $this->authorize('update', $youthAssessment);

        $validated = $this->youthAssessmentService->assignToBatchValidator($request, $id)->validate();
        $data = $this->youthAssessmentService->update($youthAssessment, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth assessment assigned to batch successfully.",
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
