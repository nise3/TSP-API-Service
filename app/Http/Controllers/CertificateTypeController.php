<?php

namespace App\Http\Controllers;

use App\Models\Cecrtificate;
use App\Services\CertificateTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CertificateTypeController extends Controller
{
    /**
     * @var CertificateTypeService
     */
    public CertificateTypeService $certificateTypeService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * CertificateController constructor.
     * @param CertificateTypeService $certificateTypeService
     */
    public function __construct(CertificateTypeService $certificateTypeService)
    {
        $this->certificateTypeService = $certificateTypeService;
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

        $filter = $this->certificateTypeService->filterValidator($request)->validate();

        $response = $this->certificateTypeService->getList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
