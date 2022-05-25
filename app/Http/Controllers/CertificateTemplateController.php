<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplates;
use App\Services\CertificateTemplatesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CertificateTemplateController extends Controller
{
    /**
     * @var CertificateTemplatesService
     */
    public CertificateTemplatesService $certificateTemplateService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * CertificateTemplateController constructor.
     * @param CertificateTemplatesService $certificateService
     */
    public function __construct(CertificateTemplatesService $certificateService)
    {
        $this->certificateTemplateService = $certificateService;
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
        $filter = $this->certificateTemplateService->filterValidator($request)->validate();

        $response = $this->certificateTemplateService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */

    public function read(Request $request, int $id): JsonResponse
    {
        $certificate = $this->certificateTemplateService->getOneCertificate($id);
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
     * @throws ValidationException
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {

        $validatedData = $this->certificateTemplateService->validator($request)->validate();
        $data = $this->certificateTemplateService->store($validatedData);
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
     * @throws ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $certificate = CertificateTemplates::findOrFail($id);

        $validated = $this->certificateTemplateService->validator($request, $id)->validate();
        $data = $this->certificateTemplateService->update($certificate, $validated);

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
        $certificate = CertificateTemplates::findOrFail($id);
        $this->certificateTemplateService->destroy($certificate);
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
