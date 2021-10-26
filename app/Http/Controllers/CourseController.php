<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
    /**
     * @var Carbon
     */

    private Carbon $startTime;

    /**
     * CourseController constructor.
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {

        $this->courseService = $courseService;
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
        $data = $this->courseService->getOneCourse($id);

        $response = [
            "data" => $data ?: [],
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
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
    public function courseDetails(int $id): JsonResponse
    {
        $course = $this->courseService->getOneCourse($id, true);
        $response = [
            "data" => $course ?: [],
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
        $validated = $this->courseService->validator($request)->validate();
        $data = $this->courseService->store($validated);

        $response = [
            'data' => $data ?: [],
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
        $validated = $this->courseService->validator($request, $id)->validate();
        $data = $this->courseService->update($course, $validated);
        $response = [
            'data' => $data ?: [],
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
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->courseService->getCourseTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @throws Throwable
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
     * @throws Throwable
     * @throws ValidationException
     */
    public function getFilterCourseList(Request $request, string $type = null): JsonResponse
    {
        $filter = $this->courseService->filterValidator($request, $type)->validate();

        $response = $this->courseService->getFilterCourses($filter, $this->startTime, $type);
        return Response::json($response);
    }
}
