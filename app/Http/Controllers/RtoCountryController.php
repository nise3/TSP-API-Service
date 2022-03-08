<?php

namespace App\Http\Controllers;

use App\Models\RtoCountry;
use App\Services\RtoCountryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class RtoCountryController extends Controller
{
    /**
     * @var RtoCountry
     */
    public  RtoCountry $rtoCountry;
    /**
     * @var RtoCountryService
     */
    public RtoCountryService $rtoCountryService ;

    /**
     * @var Carbon
     */
    private Carbon $startTime;



    public function __construct(RtoCountryService $rtoCountryService)
    {

        $this->rtoCountryService = $rtoCountryService;
        $this->startTime = Carbon::now();
    }


    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RtoCountry::class);

        $filter = $this->rtoCountryService->filterValidator($request)->validate();
        $response = $this->rtoCountryService->getRtoCountryList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {

        $this->authorize('create', RtoCountry::class);

        $validated = $this->rtoCountryService->validator($request)->validate();
        $this->rtoCountryService->store($validated);

        $response = [

            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Rto Country added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

}
