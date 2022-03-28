<?php

namespace App\Http\Controllers;

use App\Models\RplAssessmentQuestionSet;
use App\Services\RplAssessmentQuestionSetService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplAssessmentQuestionSetController extends Controller
{
    /**
     * @var RplAssessmentQuestionSetService
     */
    public RplAssessmentQuestionSetService $rplAssessmentQuestionSetService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param RplAssessmentQuestionSetService $rplAssessmentQuestionSetService
     */

    public function __construct(RplAssessmentQuestionSetService $rplAssessmentQuestionSetService)
    {
        $this->rplAssessmentQuestionSetService = $rplAssessmentQuestionSetService;
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
        $this->authorize('viewAny', RplAssessmentQuestionSet::class);
        $filter = $this->rplAssessmentQuestionSetService->filterValidator($request)->validate();

        $response = $this->rplAssessmentQuestionSetService->getAssessmentQuestionSetList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rplAssessmentQuestionSetService->filterValidator($request)->validate();

        $response = $this->rplAssessmentQuestionSetService->getAssessmentQuestionSetList($filter, $this->startTime);
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
        $assessmentQuestionSet = $this->rplAssessmentQuestionSetService->getOneAssessmentQuestionSet($id);
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
        $this->authorize('create', RplAssessmentQuestionSet::class);
        $validated = $this->rplAssessmentQuestionSetService->validator($request)->validate();
        $assessmentQuestionSet = $this->rplAssessmentQuestionSetService->store($validated);

        $response = [
            'data' => $assessmentQuestionSet,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RplAssessmentQuestionSet added successfully",
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
        $assessmentQuestionSet = RplAssessmentQuestionSet::findOrFail($id);

        $this->authorize('update', $assessmentQuestionSet);

        $validated = $this->rplAssessmentQuestionSetService->validator($request, $id)->validate();
        $data = $this->rplAssessmentQuestionSetService->update($assessmentQuestionSet, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RplAssessmentQuestionSet updated successfully.",
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
        $assessmentQuestionSet = RplAssessmentQuestionSet::findOrFail($id);

        $this->authorize('delete', $assessmentQuestionSet);

        DB::beginTransaction();
        try {
            $this->rplAssessmentQuestionSetService->destroy($assessmentQuestionSet);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RplAssessmentQuestionSet deleted successfully.",
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
