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
        $response ['total_Enroll']  = $this->instituteStatisticsService->getTotalCourseEnrollments($instituteId, $this->startTime);
        $response ['total_Course']  = $this->instituteStatisticsService->getTotalCourses($instituteId, $this->startTime);
        $response ['total_Batch']  = $this->instituteStatisticsService->getTotalBatches($instituteId, $this->startTime);
        $response ['total_running_students']  = $this->instituteStatisticsService->getTotalRunningStudents($instituteId, $this->startTime);
        $response ['total_trainers']  = $this->instituteStatisticsService->getTotalTrainers($instituteId, $this->startTime);
        $response ['total_Demand_From_Industry']  = $this->instituteStatisticsService->getTotalDemandFromIndustry($instituteId, $this->startTime);
        $response ['total_Certificate_Issue']  = $this->instituteStatisticsService->getTotalCertificateIssue($instituteId, $this->startTime);
        $response ['Total_Trending_Course']  = $this->instituteStatisticsService->getTotalTrendingCourse($instituteId, $this->startTime);
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
        $demandingCourses = $this->instituteStatisticsService->DemandingCourses($instituteId, $this->startTime);
        $response['data']=$demandingCourses;
        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


}
