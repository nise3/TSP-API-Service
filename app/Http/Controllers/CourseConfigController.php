<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\CourseConfig;
use App\Services\CourseConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Throwable;

class CourseConfigController extends Controller
{
    /**
     * @var CourseConfigService
     */
    public CourseConfigService $courseConfigService;
    private \Carbon\Carbon $startTime;

    /**
     * CourseConfigController constructor.
     * @param CourseConfigService $courseConfigService
     */
    public function __construct(CourseConfigService $courseConfigService)
    {
        $this->courseConfigService = $courseConfigService;
        $this->startTime = Carbon::now();
    }

    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->courseConfigService->getCourseConfigList($request,  $this->startTime);
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
     * Display the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function read($id): JsonResponse
    {
        try {
            $response = $this->courseConfigService->getOneCourseConfig($id, $this->startTime);
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
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->courseConfigService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $data = $this->courseConfigService->store($validatedData);
            DB::commit();
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Course Config added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
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

    public function update(Request $request, $id): JsonResponse
    {
        $courseConfig = CourseConfig::findOrFail($id);
        $validated = $this->courseConfigService->validator($request)->validate();

        try {
            $data = $this->courseConfigService->update($courseConfig, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Course config update successfully.",
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

    public function destroy($id): JsonResponse
    {
        $courseConfig = CourseConfig::findOrFail($id);

        try {
            $this->courseConfigService->destroy($courseConfig);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Course Config Delete successfully.",
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
