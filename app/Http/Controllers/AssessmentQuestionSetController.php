<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestionSet;
use App\Services\AssessmentQuestionSetService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class AssessmentQuestionSetController extends Controller
{
    /**
     * @var AssessmentQuestionSetService
     */
    public AssessmentQuestionSetService $assessmentQuestionSetService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param AssessmentQuestionSetService $assessmentQuestionSetService
     */

    public function __construct(AssessmentQuestionSetService $assessmentQuestionSetService)
    {
        $this->assessmentQuestionSetService = $assessmentQuestionSetService;
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
        $this->authorize('viewAny', AssessmentQuestionSet::class);
        $filter = $this->assessmentQuestionSetService->filterValidator($request)->validate();

        $response = $this->assessmentQuestionSetService->getAssessmentQuestionSetList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->assessmentQuestionSetService->filterValidator($request)->validate();

        $response = $this->assessmentQuestionSetService->getAssessmentQuestionSetList($filter, $this->startTime);
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
        $assessmentQuestionSet = $this->assessmentQuestionSetService->getOneAssessmentQuestionSet($id);
        $this->authorize('view', $assessmentQuestionSet);

        $response = [
            "data" => $assessmentQuestionSet,
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
        $this->authorize('create', AssessmentQuestionSet::class);
        $validated = $this->assessmentQuestionSetService->validator($request)->validate();
        $assessmentQuestionSet = $this->assessmentQuestionSetService->store($validated);

        $response = [
            'data' => $assessmentQuestionSet,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "AssessmentQuestionSet added successfully",
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
        $assessmentQuestionSet = AssessmentQuestionSet::findOrFail($id);

        $this->authorize('update', $assessmentQuestionSet);

        $validated = $this->assessmentQuestionSetService->validator($request, $id)->validate();
        $data = $this->assessmentQuestionSetService->update($assessmentQuestionSet, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "AssessmentQuestionSet updated successfully.",
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
        $assessmentQuestionSet = AssessmentQuestionSet::findOrFail($id);

        $this->authorize('delete', $assessmentQuestionSet);

        DB::beginTransaction();
        try {
            $this->assessmentQuestionSetService->destroy($assessmentQuestionSet);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "AssessmentQuestionSet deleted successfully.",
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