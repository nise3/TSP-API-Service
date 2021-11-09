<?php

namespace App\Http\Controllers;

use App\Models\Institute;
use Exception;
use Illuminate\Http\Client\RequestException;
use \Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Services\InstituteService;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class InstituteController extends Controller
{
    /**
     * @var InstituteService
     */
    public InstituteService $instituteService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * InstituteController constructor.
     * @param InstituteService $instituteService
     */
    public function __construct(InstituteService $instituteService)
    {
        $this->instituteService = $instituteService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->instituteService->filterValidator($request)->validate();

        $response = $this->instituteService->getInstituteList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $data = $this->instituteService->getOneInstitute($id);

        $response = [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     * @throws RequestException
     */
    public function store(Request $request): JsonResponse
    {
        /** @var Institute $institute */
        $institute = app(Institute::class);
        $validatedData = $this->instituteService->validator($request)->validate();

        DB::beginTransaction();

        try {

            $institute = $this->instituteService->store($institute, $validatedData);

            if (!($institute && $institute->id)) {
                throw new RuntimeException('Saving Institute to DB failed!', 500);
            }

            $validatedData['institute_id'] = $institute->id;
            $createdUser = $this->instituteService->createUser($validatedData);
            Log::channel('idp_user')->info('idp_user_info:' . json_encode($createdUser));

            if (!($createdUser && !empty($createdUser['_response_status']))) {
                throw new RuntimeException('Creating User during Institute Creation has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Institute Successfully Created",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];

            if (isset($createdUser['_response_status']['success']) && $createdUser['_response_status']['success']) {
                DB::commit();
                $response['data'] = $institute;
                return Response::json($response, ResponseAlias::HTTP_CREATED);
            }

            DB::rollBack();

            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;
            if (!empty($createdUser['_response_status']['code'])) {
                $httpStatusCode = $createdUser['_response_status']['code'];
            }

            $response['_response_status'] = [
                "success" => false,
                "code" => $httpStatusCode,
                "message" => "Error Occurred. Please Contact.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ];

            if (!empty($createdUser['errors'])) {
                $response['errors'] = $createdUser['errors'];
            }

            return Response::json($response, $httpStatusCode);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function instituteRegistration(Request $request): JsonResponse
    {

        $institute = app(Institute::class);
        $validated = $this->instituteService->registerInstituteValidator($request)->validate();

        DB::beginTransaction();
        try {
            $institute = $this->instituteService->store($institute, $validated);

            if (!($institute && $institute->id)) {
                throw new RuntimeException('Saving Institute to DB failed!', 500);
            }

            $validated['institute_id'] = $institute->id;
            $createdRegisterUser = $this->instituteService->createRegisterUser($validated);

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during Institute Registration has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Institute Successfully Created",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];

            if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {
                DB::commit();
                $response['data'] = $institute;
                return Response::json($response, ResponseAlias::HTTP_CREATED);
            }

            DB::rollBack();

            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;
            if (!empty($createdRegisterUser['_response_status']['code'])) {
                $httpStatusCode = $createdRegisterUser['_response_status']['code'];
            }

            $response['_response_status'] = [
                "success" => false,
                "code" => $httpStatusCode,
                "message" => "Error Occurred. Please Contact.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ];

            if (!empty($createdRegisterUser['errors'])) {
                $response['errors'] = $createdRegisterUser['errors'];
            }

            return Response::json($response, $httpStatusCode);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);
        $validated = $this->instituteService->validator($request, $id)->validate();
        $data = $this->instituteService->update($institute, $validated);
        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Institute updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);
        $this->instituteService->destroy($institute);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Institute deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->instituteService->getInstituteTrashList($request, $this->startTime);
        return Response::json($response);
    }

    /**
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $institute = Institute::onlyTrashed()->findOrFail($id);
        $this->instituteService->restore($institute);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Institute restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $institute = Institute::onlyTrashed()->findOrFail($id);
        $this->instituteService->forceDelete($institute);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Institute permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
