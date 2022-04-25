<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\CertificateIssued;
use App\Services\CertificateIssuedService;
use App\Services\CommonServices\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CertificateIssuedController extends Controller
{
    /**
     * @var CertificateIssuedService
     */
    public CertificateIssuedService $certificateIssuedService;
    /**
     * @var \Carbon\Carbon|Carbon
     */
    private \Carbon\Carbon|Carbon $startTime;

    /**
     * CertificateController constructor.
     * @param CertificateService $certificateIssuedService
     */
    public function __construct(CertificateIssuedService $certificateIssuedService)
    {
        $this->certificateIssuedService = $certificateIssuedService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny', Certificate::class);

        $filter = $this->certificateIssuedService->filterValidator($request)->validate();

        $response = $this->certificateIssuedService->getList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */

    public function read(Request $request, int $id): JsonResponse
    {
        $certificate = $this->certificateIssuedService->getOneIssuedCertificate($id);
        $response = [
            "data" => $certificate,
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
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->certificateIssuedService->validator($request)->validate();
        $data = $this->certificateIssuedService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Certificate Issued successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        if (isset($createdUser['_response_status']['success']) && $createdUser['_response_status']['success']){
            /** Mail send after user registration */
//            $to = array($validatedData['contact_person_email']);
            $to = 'grmunnabd@gmail.com';
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "User Registration Information";
            $message = "Congratulation, A certificate is issued for you. Your can download from here : ";
            $messageBody = MailService::templateView($message);

            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $certificateIssuedOne = CertificateIssued::findOrFail($id);

        $validated = $this->certificateIssuedService->validator($request, $id)->validate();

        $data = $this->certificateIssuedService->update($certificateIssuedOne, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate template updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */

    public function destroy(int $id): JsonResponse
    {
        $certificate = CertificateIssuedService::findOrFail($id);
        $this->certificateIssuedService->destroy($certificate);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate template deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
