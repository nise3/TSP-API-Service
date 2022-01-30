<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\CourseEnrollmentService;
use App\Services\CourseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CourseController extends Controller
{
    /**
     * @var CourseService
     */
    public CourseService $courseService;
    public CourseEnrollmentService $courseEnrollmentServiceService;
    /**
     * @var Carbon
     */

    private Carbon $startTime;

    /**
     * CourseController constructor.
     * @param CourseService $courseService
     * @param CourseEnrollmentService $courseEnrollmentService
     */
    public function __construct(CourseService $courseService, CourseEnrollmentService $courseEnrollmentService)
    {

        $this->courseService = $courseService;
        $this->courseEnrollmentServiceService = $courseEnrollmentService;
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
        $this->authorize('viewAny', Course::class);

        $filter = $this->courseService->filterValidator($request)->validate();
        $response = $this->courseService->getCourseList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $course = $this->courseService->getOneCourse($id);

        $this->authorize('view', $course);

        $response = [
            "data" => $course,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('create', Course::class);

        $validated = $this->courseService->validator($request)->validate();
        $validated['code'] = CodeGeneratorService::getCourseCode();
        $course = $this->courseService->store($validated);

        $response = [
            'data' => $course,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Course added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $this->authorize('update', $course);

        $validated = $this->courseService->validator($request, $id)->validate();
        $data = $this->courseService->update($course, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Course updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $this->authorize('delete', $course);

        $this->courseService->destroy($course);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Course deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function publicCourseDetails(int $id): JsonResponse
    {
        $course = $this->courseService->getOneCourse($id, true);
        $response = [
            "data" => $course ?: null,
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
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->courseService->getCourseTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $this->courseService->restore($course);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Course restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $this->courseService->forceDelete($course);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Course permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param string|null $type
     * @return JsonResponse
     * @throws ValidationException
     */
    //TODO: config for industry association
    public function getFilterCourseList(Request $request, string $type = null): JsonResponse
    {
        $filter = $this->courseService->filterValidator($request, $type)->validate();

        $response = $this->courseService->getFilterCourses($filter, $this->startTime, $type);
        return Response::json($response);
    }

    /**
     * @param Request $request
     * @param int $youthId
     * @return array
     */
    public function youthFeedStatistics(Request $request, int $youthId): array
    {
        $requestData = $request->all();
        if (!empty($requestData["skill_ids"])) {
            $requestData["skill_ids"] = is_array($requestData['skill_ids']) ? $requestData['skill_ids'] : explode(',', $requestData['skill_ids']);
        }
        $totalCourseCount = $this->courseService->getCourseCount();
        $enrolledCourseCount = $this->courseEnrollmentServiceService->getEnrolledCourseCount($youthId);
        $skillMatchingCourseCount = 0;
        if (!empty($requestData["skill_ids"]) && is_array($requestData["skill_ids"]) && count($requestData["skill_ids"]) > 0) {
            $skillMatchingCourseCount = $this->courseService->getSkillMatchingCourseCount($requestData["skill_ids"]);
        }

        return [
            'total_courses' => $totalCourseCount,
            'enrolled_courses' => $enrolledCourseCount,
            'skill_matching_courses' => $skillMatchingCourseCount
        ];
    }

}
