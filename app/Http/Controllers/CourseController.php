<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\Course;
use App\Services\CourseService;
use Carbon\Carbon;
use Exception;
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
     *  @return Exception|JsonResponse|Throwable
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->courseService->getCourseList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function read(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->courseService->getOneCourse($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->courseService->validator($request)->validate();
        try {
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
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * update the specified resource in storage
     * @param Request $request
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $validated = $this->courseService->validator($request, $id)->validate();
        try {
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
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  *  remove the specified resource from storage
     * @param int $id
     *  @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        try {
            $this->courseService->destroy($course);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Course deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
