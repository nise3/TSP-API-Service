<?php

namespace App\Http\Controllers;

use App\Models\Institute;
use Exception;
use \Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Services\InstituteService;
use Illuminate\Validation\ValidationException;
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
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->instituteService->filterValidator($request)->validate();

        try {
            $response = $this->instituteService->getInstituteList($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * * Display the specified resource
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->instituteService->getOneInstitute($id, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        $institute = new Institute();
        $validatedData = $this->instituteService->validator($request)->validate();
        try {
            $institute = $this->instituteService->store($institute, $validatedData);
            if ($institute) {
                $validatedData['institute_id'] = $institute->id;
                $createUser = $this->instituteService->createUser($validatedData);
                Log::channel('idp_user')->info('idp_user_info:' . json_encode($createUser));
                if ($createUser && $createUser['_response_status']['success']) {
                    $response = [
                        'data' => $institute ?: [],
                        '_response_status' => [
                            "success" => true,
                            "code" => ResponseAlias::HTTP_CREATED,
                            "message" => "Institute added successfully",
                            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                        ]
                    ];
                    DB::commit();
                } else {
                    if ($createUser && $createUser['_response_status']['code'] == 400) {
                        $response = [
                            'errors' => $createUser['errors'] ?? [],
                            '_response_status' => [
                                "success" => false,
                                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                                "message" => "Validation Error",
                                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                            ]
                        ];
                    } else {
                        $response = [
                            '_response_status' => [
                                "success" => false,
                                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                                "message" => "Unprocessable Request,Please contact",
                                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                            ]
                        ];
                    }

                    DB::rollBack();
                }
            }

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function instituteRegistration(Request $request)
    {

        $institute = new Institute();

        $validated = $this->instituteService->registerInstituteValidator($request)->validate();
        DB::beginTransaction();
        try {
            $institute = $this->instituteService->store($institute, $validated);

            if ($institute) {

                $validated['institute_id'] = $institute->id;
                $createRegisterUser = $this->instituteService->createRegisterUser($validated);

                if ($createRegisterUser && $createRegisterUser['_response_status']['success']) {
                    $response = [
                        'data' => $institute ?: [],
                        '_response_status' => [
                            "success" => true,
                            "code" => ResponseAlias::HTTP_CREATED,
                            "message" => "Institute Successfully Create",
                            "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                        ]
                    ];
                    DB::commit();
                } else {
                    if ($createRegisterUser && $createRegisterUser['_response_status']['code'] == ResponseAlias::HTTP_UNPROCESSABLE_ENTITY) {
                        $response = [
                            'errors' => $createRegisterUser['errors'] ?? [],
                            '_response_status' => [
                                "success" => false,
                                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                                "message" => "Validation Error",
                                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
                            ]
                        ];
                    } else {
                        $response = [
                            '_response_status' => [
                                "success" => false,
                                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                                "message" => "Unprocessable Request,Please contact",
                                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                            ]
                        ];
                    }

                    DB::rollBack();
                }
            }

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);
        $validated = $this->instituteService->validator($request, $id)->validate();
        try {
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
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  * Remove the specified resource from storage.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);
        try {
            $this->instituteService->destroy($institute);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Institute deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function getTrashedData(Request $request)
    {
        try {
            $response = $this->instituteService->getInstituteTrashList($request, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    public function restore(int $id)
    {
        $institute = Institute::onlyTrashed()->findOrFail($id);
        try {
            $this->instituteService->restore($institute);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Institute restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function forceDelete(int $id)
    {
        $institute = Institute::onlyTrashed()->findOrFail($id);
        try {
            $this->instituteService->forceDelete($institute);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Institute permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
