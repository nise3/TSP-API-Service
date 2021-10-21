<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\CourseEnrollmentService;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CourseEnrollmentController extends Controller
{

    /**
     * @var CourseEnrollmentService
     */
    public CourseEnrollmentService $courseEnrollService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * CourseEnrollmentController constructor.
     * @param CourseEnrollmentService $courseEnrollService
     */
    public function __construct(CourseEnrollmentService $courseEnrollService)
    {
        $this->courseEnrollService = $courseEnrollService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->filterValidator($request)->validate();
        $response = $this->courseEnrollService->getCourseEnrollmentList($validated, $this->startTime);
        return Response::json($response);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getYouthEnrollCourses(Request $request): JsonResponse
    {
        $validated = $this->courseEnrollService->youthEnrollCoursesFilterValidator($request)->validate();
        $response = $this->courseEnrollService->getYouthEnrollCourses($validated, $this->startTime);
        return Response::json($response);
    }


    /**
     * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->courseEnrollService->getOneCourseEnrollment($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function courseEnrollment(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $validated = $this->courseEnrollService->courseEnrollmentValidator($request)->validate();
        try {
            $courseEnroll = $this->courseEnrollService->enrollCourse($validated);
            $this->courseEnrollService->storeEnrollmentAddresses($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentEducations($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentProfessionalInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentGuardianInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentMiscellaneousInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentPhysicalDisabilities($validated, $courseEnroll);

            $this->updateYouthProfileAfterEnrollment($validated);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Course enroll successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param array $data
     * @return PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RequestException
     */
    public function updateYouthProfileAfterEnrollment(array $data)
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'youth-update-after-course-enrollment';

        return Http::withOptions([
            'verify' => false,
            'timeout' => 60
        ])
            ->post($url, $data)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ', (array)$response);
                return $e;
            })
            ->json();
    }
}
