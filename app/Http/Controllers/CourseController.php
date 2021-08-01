<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\Course;
use App\Services\CourseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
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
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->courseService->getCourseList($request, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);
    }

    /**
     * Display the specified resource
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function read(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->courseService->getOneCourse($id, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->courseService->validator($request)->validate();
        try {
            $data = $this->courseService->store($validated);

            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Course added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * update the specified resource in storage
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $validated = $this->courseService->validator($request, $id)->validate();
        try {
            $data = $this->courseService->update($course, $validated);
            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Course updated successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     *  remove the specified resource from storage
     * @param $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        try {
            $this->courseService->destroy($course);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Course deleted successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response, JsonResponse::HTTP_OK);
    }
}
