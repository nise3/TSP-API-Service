<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\RegisteredTrainingOrganization;
use App\Services\CommonServices\CodeGeneratorService;
use App\Services\CommonServices\MailService;
use App\Services\RegisteredTrainingOrganizationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RegisteredTrainingOrganizationController extends Controller
{
    /**
     * @var RegisteredTrainingOrganizationService
     */
    public RegisteredTrainingOrganizationService $rtoService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * InstituteController constructor.
     * @param RegisteredTrainingOrganizationService $rtoService
     */

    public function __construct(RegisteredTrainingOrganizationService $rtoService)
    {
        $this->rtoService = $rtoService;
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
        $this->authorize('viewAny', RegisteredTrainingOrganization::class);

        $filter = $this->rtoService->filterValidator($request)->validate();

        $response = $this->rtoService->getRtoList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rtoService->filterValidator($request)->validate();

        $response = $this->rtoService->getRtoList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * * Display the specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $rto = $this->rtoService->getOneRto($id);
        $this->authorize('view', $rto);

        $response = [
            "data" => $rto,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function rtoDetails(int $id): JsonResponse
    {

        $data = $this->rtoService->getOneRto($id);

        $response = [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Call from public landing page
     * @throws Throwable
     */
    public function rtoPublicDetails(): JsonResponse
    {
        /** this should be set from PublicApiMiddleWare */
        $rtoId = request()->get('registered_training_organization_id');
        $data = $this->rtoService->getOneRto($rtoId);

        $response = [
            "data" => $data,
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
        /** @var RegisteredTrainingOrganization $rto */
        $rto = app(RegisteredTrainingOrganization::class);

        $this->authorize('create', $rto);

        $validatedData = $this->rtoService->validator($request)->validate();
        $validatedData['code'] = CodeGeneratorService::getRTOCode();
        DB::beginTransaction();

        try {

            $rto = $this->rtoService->store($rto, $validatedData);

            if (!($rto && $rto->id)) {
                throw new RuntimeException('Saving RTO to DB failed!', 500);
            }

            $validatedData['registered_training_organization_id'] = $rto->id;

            $validatedData['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

            $createdUser = $this->rtoService->createUser($validatedData);
            Log::channel('idp_user')->info('idp_user_info:' . json_encode($createdUser));

            if (!($createdUser && !empty($createdUser['_response_status']))) {
                throw new RuntimeException('Creating User during RTO Creation has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "RTO Successfully Created",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];


            if (isset($createdUser['_response_status']['success']) && $createdUser['_response_status']['success']) {

                /** Mail send after user registration */
                $to = array($validatedData['contact_person_email']);
                $from = BaseModel::NISE3_FROM_EMAIL;
                $subject = "User Registration Information";
                $message = "Congratulation, You are successfully complete your registration as " . $validatedData['title'] . " user. Your Username: " . $validatedData['contact_person_mobile'] . " and Password: " . $validatedData['password'];
                $messageBody = MailService::templateView($message);

                $mailService = new MailService($to, $from, $subject, $messageBody);
                $mailService->sendMail();

                /** SMS send after user registration */
                $recipient = $validatedData['contact_person_mobile'];
                $message = "Congratulation, You are successfully complete your registration as a " . $validatedData['title'] . " user";
                $this->rtoService->userInfoSendBySMS($recipient, $message);

                DB::commit();
                $response['data'] = $rto;
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
        $rto = RegisteredTrainingOrganization::findOrFail($id);

        $this->authorize('update', $rto);

        $validated = $this->rtoService->validator($request, $id)->validate();
        $data = $this->rtoService->update($rto, $validated);
        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RTO updated successfully.",
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
        $rto = RegisteredTrainingOrganization::findOrFail($id);

        $this->authorize('delete', $rto);

        DB::beginTransaction();
        try {
            $this->rtoService->destroy($rto);
            $this->rtoService->rtoUserDestroy($rto);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RTO deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
