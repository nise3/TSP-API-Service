<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Institute;
use App\Services\CourseService;
use App\Services\ProgramService;
use Illuminate\Http\Client\RequestException;
use \Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public CourseService $courseService;
    public ProgramService $programService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * InstituteController constructor.
     * @param InstituteService $instituteService
     * @param CourseService $courseService
     * @param ProgramService $programService
     */
    public function __construct(InstituteService $instituteService, CourseService $courseService, ProgramService $programService)
    {
        $this->instituteService = $instituteService;
        $this->courseService = $courseService;
        $this->programService = $programService;
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
        $this->authorize('viewAny', Institute::class);
        $filter = $this->instituteService->filterValidator($request)->validate();

        $response = $this->instituteService->getInstituteList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * * Display the specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function read(Request $request,int $id): JsonResponse
    {
        $institute = $this->instituteService->getOneInstitute($id);

        $requestHeaders = $request->header();
        /** Policy not checking when service to service call true*/
        if (empty($requestHeaders[BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_KEY][0]) ||
            $requestHeaders[BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_KEY][0] === BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_FLAG_FALSE) {
            $this->authorize('view', $institute);
        }

        $response = [
            "data" => $institute,
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
     * @throws RequestException
     */
    public function store(Request $request): JsonResponse
    {
        /** @var Institute $institute */
        $institute = app(Institute::class);

        $this->authorize('create', $institute);

        $validatedData = $this->instituteService->validator($request)->validate();

        DB::beginTransaction();

        try {

            $institute = $this->instituteService->store($institute, $validatedData);

            if (!($institute && $institute->id)) {
                throw new RuntimeException('Saving Institute to DB failed!', 500);
            }

            $validatedData['institute_id'] = $institute->id;

            $validatedData['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

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

                $this->instituteService->userInfoSendByMail($validatedData);

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

        $this->authorize('update', $institute);

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
     *Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $institute = Institute::findOrFail($id);

        $this->authorize('delete', $institute);

        DB::beginTransaction();
        try {
            $this->instituteService->destroy($institute);
            $this->instituteService->instituteUserDestroy($institute);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Institute deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Institute Open Registration
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
                $response['data'] = $institute;


                $this->instituteService->userInfoSendByMail($validated);

                DB::commit();
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
     * @throws Throwable
     */
    public function getInstituteTitleByIds(Request $request): JsonResponse
    {
        throw_if(!is_array($request->get('institute_ids')), ValidationException::withMessages([
            "The Organization ids must be array.[8000]"
        ]));

        $organizationTitle = $this->instituteService->getInstituteTitle($request);
        $response = [
            "data" => $organizationTitle,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Institute Title List.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Throwable
     */
    public function getCourseAndProgramTitleByIds(Request $request): JsonResponse
    {
        throw_if(!empty($request->input('course_ids')) && !is_array($request->input('course_ids')), ValidationException::withMessages([
            "The Course ids must be an array.[8000]"
        ]));
        throw_if(!empty($request->input('program_ids')) && !is_array($request->input('program_ids')), ValidationException::withMessages([
            "The Program ids must be an array.[8000]"
        ]));

        $courseTitle = $this->courseService->getCourseTitle($request);
        $programTitle = $this->programService->getProgramTitle($request);

        $response = [
            "data" => [
                'course_title' => $courseTitle,
                'program_title' => $programTitle
            ],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "batch and Program Title List.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now())
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

    /**
     * Institute Open Registration Approval
     * @param int $instituteId
     * @return JsonResponse
     * @throws Throwable
     */
    public function instituteRegistrationApproval(int $instituteId): JsonResponse
    {
        $institute = Institute::findOrFail($instituteId);

        DB::beginTransaction();
        try {
            if ($institute && $institute->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->instituteService->InstituteStatusChangeAfterApproval($institute);
                $this->instituteService->InstituteUserApproval($institute);
                DB::commit();
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "Institute Registration  approved successfully",
                        "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "No pending status found for this Institute",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            }


        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * Institute Open Registration Rejection
     * @param int $instituteId
     * @return JsonResponse
     * @throws Throwable
     */
    public function InstituteRegistrationRejection(int $instituteId): JsonResponse
    {
        $institute = Institute::findOrFail($instituteId);

        DB::beginTransaction();
        try {
            if ($institute && $institute->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->instituteService->InstituteStatusChangeAfterRejection($institute);
                $this->instituteService->InstituteUserRejection($institute);
                DB::commit();
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "Institute Registration  rejected successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "No pending status found for this Institute",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            }


        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


    public function updateInstituteAdminProfile(Request $request): JsonResponse
    {
        $authUser = Auth::user();
        $instituteId = null;
        if ($authUser && $authUser->institute_id) {
            $instituteId = $authUser->institute_id;
        }
        $institute = Institute::findOrFail($instituteId);

        $this->authorize('update', $institute);

        $validated = $this->instituteService->instituteAdminProfileValidator($request, $instituteId)->validate();
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



}
