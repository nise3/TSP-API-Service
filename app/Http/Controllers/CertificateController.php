<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CertificateController extends Controller
{
    /**
     * @var CertificateService
     */
    public CertificateService $certificateService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * CertificateController constructor.
     * @param CertificateService $certificateService
     */
    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny', Certificate::class);
        $filter = $this->certificateService->filterValidator($request)->validate();
//        dd($filter);
        $response = $this->certificateService->getList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */

    public function read(Request $request, int $id): JsonResponse
    {
        $certificate = $this->certificateService->getOneExamSubject($id);
        $response = [
            "data" => $certificate,
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
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {

        $validatedData = $this->certificateService->validator($request)->validate();
        $data = $this->certificateService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Certificate template added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $examSubject = Certificate::findOrFail($id);

        $validated = $this->certificateService->validator($request, $id)->validate();

        $data = $this->certificateService->update($examSubject, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate template updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */

    public function destroy(int $id): JsonResponse
    {
        $certificate = Certificate::findOrFail($id);
        $this->certificateService->destroy($certificate);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate template deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
