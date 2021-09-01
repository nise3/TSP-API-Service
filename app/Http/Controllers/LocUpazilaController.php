<?php

namespace App\Http\Controllers;


//use App\Models\LocUpazila;

use App\Services\LocationManagementServices\LocUpazilaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class LocUpazilaController extends Controller
{
    /**
     * @var locUpazilaService
     */
    public LocUpazilaService $locUpazilaService;
    public Carbon $startTime;

    /**
     * LocUpazilaController constructor.
     * @param LocUpazilaService $locUpazilaService
     * @param Carbon $startTime
     */
    public function __construct(LocUpazilaService $locUpazilaService, Carbon $startTime)
    {
        $this->locUpazilaService = $locUpazilaService;
        $this->startTime = $startTime;
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->locUpazilaService->filterValidator($request)->validate();

        try {
            $response = $this->locUpazilaService->getAllUpazilas($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Exception|JsonResponse|Throwable
     */
    public function read(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->locUpazilaService->getOneUpazila($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }
}
