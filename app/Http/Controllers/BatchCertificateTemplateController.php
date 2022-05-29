<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Result;
use App\Services\BatchCertificateTemplateService;
use App\Services\CommonServices\CodeGeneratorService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class BatchController
 * @package App\Http\Controllers
 */
class BatchCertificateTemplateController extends Controller
{
    /**
     * @var BatchCertificateTemplateService
     */
    public BatchCertificateTemplateService $batchCertificateTemplateService;

    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * BatcheCertificateTemplateController constructor.
     * @param BatchCertificateTemplateService $batchCertTemplService
     */
    public function __construct(BatchCertificateTemplateService $batchCertTemplService)
    {
        $this->batchCertificateTemplateService = $batchCertTemplService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function getListByBatchId(Request $request, int $id): JsonResponse
    {
        $filter = $this->batchCertificateTemplateService->filterValidator($request)->validate();
        $response = $this->batchCertificateTemplateService->getListByBatchId($filter, $id);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function getListByBatchIds(Request $request, $ids): JsonResponse
    {
        $arrayIds = explode(',',$ids);
        $filter = $this->batchCertificateTemplateService->filterValidator($request)->validate();
        $response = $this->batchCertificateTemplateService->getListByBatchIds($filter, $arrayIds);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
