<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use App\Models\RplQuestionBank;
use App\Services\AssessmentQuestionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class AssessmentQuestionController extends Controller
{
    /**
     * @var AssessmentQuestionService
     */
    public AssessmentQuestionService $assessmentQuestionService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * QuestionBankController constructor.
     * @param AssessmentQuestionService $assessmentQuestionService
     */

    public function __construct(AssessmentQuestionService $assessmentQuestionService)
    {
        $this->assessmentQuestionService = $assessmentQuestionService;
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
        $this->authorize('viewAny', AssessmentQuestion::class);
        $filter = $this->assessmentQuestionService->filterValidator($request)->validate();

        $response = $this->assessmentQuestionService->getAssessmentQuestionList($filter, $this->startTime);
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
        $filter = $this->assessmentQuestionService->publicFilterValidator($request)->validate();

        $response = $this->assessmentQuestionService->getAssessmentQuestionList($filter, $this->startTime, true);
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
        $this->authorize('create', AssessmentQuestion::class);
        $validated = $this->assessmentQuestionService->validator($request)->validate();
        $this->assessmentQuestionService->store($validated);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Assessment Question  added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

}
