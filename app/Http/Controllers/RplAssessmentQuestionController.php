<?php

namespace App\Http\Controllers;

use App\Models\RplAssessmentQuestion;
use App\Services\RplAssessmentQuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplAssessmentQuestionController extends Controller
{
    /**
     * @var RplAssessmentQuestionService
     */
    public RplAssessmentQuestionService $rplAssessmentQuestionService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * QuestionBankController constructor.
     * @param RplAssessmentQuestionService $rplAssessmentQuestionService
     */

    public function __construct(RplAssessmentQuestionService $rplAssessmentQuestionService)
    {
        $this->rplAssessmentQuestionService = $rplAssessmentQuestionService;
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
        $this->authorize('viewAny', RplAssessmentQuestion::class);
        $filter = $this->rplAssessmentQuestionService->filterValidator($request)->validate();

        $response = $this->rplAssessmentQuestionService->getAssessmentQuestionList($filter, $this->startTime);
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
        $filter = $this->rplAssessmentQuestionService->publicFilterValidator($request)->validate();

        $response = $this->rplAssessmentQuestionService->getAssessmentQuestionList($filter, $this->startTime, true);
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
        $this->authorize('create', RplAssessmentQuestion::class);
        $validated = $this->rplAssessmentQuestionService->validator($request)->validate();
        $this->rplAssessmentQuestionService->store($validated);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RplAssessment Question  added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

}
