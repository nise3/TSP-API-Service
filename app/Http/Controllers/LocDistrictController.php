<?php

namespace App\Http\Controllers;

//use App\Models\LocDistrict;
use App\Services\LocationManagementServices\LocDistrictService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class LocDistrictController extends Controller
{
    /**
     * @var locDistrictService
     */
    public LocDistrictService $locDistrictService;
    private Carbon $startTime;

    /**
     * LocDistrictController constructor.
     * @param LocDistrictService $locDistrictService
     */
    public function __construct(LocDistrictService $locDistrictService)
    {
        $this->startTime = Carbon::now();

        $this->locDistrictService = $locDistrictService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->locDistrictService->filterValidator($request)->validate();

        try {
            $response = $this->locDistrictService->getAllDistricts($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->locDistrictService->getOneDistrict($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }
}
