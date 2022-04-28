<?php

namespace App\Http\Controllers;

use App\Models\ExamSubject;
use App\Services\CertificateService;
use App\Services\ExamSubjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ExamSubjectController extends Controller
{
    /**
     * @var CertificateService
     */
    public CertificateService $ExamSubjectService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * @param ExamSubjectService $examSubjectService
     * @param CertificateService $ExamSubjectService
     */

    public function __construct(CertificateService $examSubjectService)
    {
        $this->ExamSubjectService = $examSubjectService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */

    public function getList(Request $request): JsonResponse
    {

        $filter = $this->ExamSubjectService->filterValidator($request)->validate();
        $response = $this->ExamSubjectService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function read(Request $request, int $id): JsonResponse
    {
        $examSubject = $this->ExamSubjectService->getOneExamSubject($id);
        $this->authorize('view',$examSubject);

        $response = [
            "data" => $examSubject,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create',ExamSubject::class);
        $validatedData = $this->ExamSubjectService->validator($request)->validate();
        $data = $this->ExamSubjectService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Exam Subject added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */

    public function update(Request $request, int $id): JsonResponse
    {

        $examSubject = ExamSubject::findOrFail($id);
        $this->authorize('update',$examSubject);
        $validated = $this->ExamSubjectService->validator($request, $id)->validate();

        $data = $this->ExamSubjectService->update($examSubject, $validated);



        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Exam Subject updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function destroy(int $id): JsonResponse
    {
        $examSubject = ExamSubject::findOrFail($id);
        $this->authorize('delete',$examSubject);
        $this->ExamSubjectService->destroy($examSubject);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Exam Subject deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
