<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Services\CourseEnrollmentService;
use App\Services\InstituteService;
use App\Services\InstituteStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class InstituteStatisticsController extends Controller
{

    public InstituteStatisticsService $instituteStatisticsService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * InstituteController constructor.
     * @param InstituteStatisticsService $instituteStatisticsService
     */
    public function __construct(InstituteStatisticsService $instituteStatisticsService)
    {
        $this->instituteStatisticsService = $instituteStatisticsService;
        $this->startTime = Carbon::now();
    }

    public function dashboardStatistics(int $instituteId=null): JsonResponse
    {;
        $authUser = Auth::user();
        if ($authUser  && $authUser->institute_id) {  //Institute User
            $instituteId = $authUser->institute_id;
        }
        $response['data'] = $this->instituteStatisticsService->finalStatisticalData($instituteId);
        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    public function DemandingCourses(int $instituteId=null): JsonResponse
    {
        $authUser = Auth::user();
        if ($authUser  && $authUser->institute_id) {  //Institute User
            $instituteId = $authUser->institute_id;
        }
        $demandingCourses = $this->instituteStatisticsService->DemandingCourses($instituteId);
        $response['data']=$demandingCourses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


}
