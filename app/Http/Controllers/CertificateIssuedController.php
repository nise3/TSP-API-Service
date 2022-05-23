<?php

namespace App\Http\Controllers;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\CertificateIssued;
use App\Models\Course;
use App\Services\BatchService;
use App\Services\CertificateIssuedService;
use App\Services\CertificateService;
use App\Services\CommonServices\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

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
     * @param CertificateIssuedService $certificateIssuedService
     */
    public function __construct(
        CertificateIssuedService $certificateIssuedService
    )
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
     * @throws ValidationException
     */
    public function getCertificateList(Request $request, int $fourIrInitiativeId): JsonResponse
    {
        $curseIds = Course::where("", $fourIrInitiativeId)->pluck("id")->toArray();
        $request->offsetSet('course_id', $curseIds);
        $filter = $this->certificateIssuedService->filterValidator($request)->validate();
        $response = $this->certificateIssuedService->getList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws ValidationException
     */
    public function getCertificateIssuedByYouthId(int $youthId, int $courseId): JsonResponse
    {
//        Log::info('log info ' . $youthId . ' ' . $courseId);
        $certificateIssued = CertificateIssued::where('youth_id', $youthId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        $response = [
            "data" => $this->certificateIssuedService->getOneIssuedCertificate($certificateIssued->id),
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
     * @throws ValidationException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->certificateIssuedService->validator($request)->validate();

        DB::beginTransaction();

        try {
            $data = $this->certificateIssuedService->store($validatedData);
            $this->certificateIssuedService->certificateIssuedAtUpdate($validatedData['certificate_id']);
            $this->certificateIssuedService->courseEnrollmentUpdate($validatedData, $data);
            DB::commit();
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Certificate Issued successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }


        $youth = ServiceToServiceCall::getYouthProfilesByIds([$data->youth_id])[0];
        if (isset($data['_response_status']['success']) && $data['_response_status']['success']) {
            /** Mail send after certificate issued */
            $to = array($youth['email']);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "Certificate Issued";
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
     * @throws ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {
        $certificateIssued = CertificateIssued::findOrFail($id);

        $validated = $this->certificateIssuedService->validator($request, $id)->validate();

        $data = $this->certificateIssuedService->update($certificateIssued, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate Issued updated successfully.",
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
        $certificate = CertificateIssued::findOrFail($id);
        $this->certificateIssuedService->destroy($certificate);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Certificate Issued deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
