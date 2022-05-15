<?php

namespace App\Http\Controllers;

use App\Models\CourseResultConfig;
use App\Services\CourseResultConfigService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CourseResultConfigController extends Controller
{
    /**
     * @var CourseResultConfigService
     */
    public CourseResultConfigService $courseResultConfigService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * @param CourseResultConfigService $courseResultConfigService
     */

    public function __construct(CourseResultConfigService $courseResultConfigService)
    {
        $this->courseResultConfigService = $courseResultConfigService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */

    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny',CourseResultConfig::class);
        $filter = $this->courseResultConfigService->filterValidator($request)->validate();
        $response = $this->courseResultConfigService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function read(Request $request, int $id): JsonResponse
    {
        $courseResultConfig = $this->courseResultConfigService->getOneCourseResultConfig($id);
        //$this->authorize('view',$courseResultConfig);

        $response = [
            "data" => $courseResultConfig,
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
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        //$this->authorize('create',CourseResultConfig::class);
        $validatedData = $this->courseResultConfigService->validator($request)->validate();
        $data = $this->courseResultConfigService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function destroy(int $id): JsonResponse
    {
        $courseResultConfig = CourseResultConfig::findOrFail($id);
        //$this->authorize('delete',$courseResultConfig);
        $this->courseResultConfigService->destroy($courseResultConfig);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Course Result Config deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
