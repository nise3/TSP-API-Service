<?php

namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Certificate;
use App\Models\CertificateIssued;
use App\Models\CourseEnrollment;
use App\Models\ExamAnswer;
use App\Models\RplSubject;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CertificateIssuedService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $pageSize = $request['page_size'] ?? BaseModel::DEFAULT_PAGE_SIZE;
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        $batchIds = $request['batch_id'] ?? [];
        $youthIds = $request['youth_id'] ?? [];
        $courseId = $request['course_id'] ?? [];
        $certificateIds = $request['certificate_id'] ?? [];

        /** @var CertificateIssued|Builder $certificateIssuedBuilder */
        $certificateIssuedBuilder = CertificateIssued::select([
            'certificate_issued.id',
            'certificate_issued.certificate_id',
            'certificates.title as certificate_title',
            'certificates.title_en as certificate_title_en',
            'certificates.result_type as certificate_result_type',
            'certificates.issued_at',
            'certificate_issued.youth_id',
            'certificate_issued.course_id',
            'certificate_issued.batch_id',
            'batches.title as batch_title',
            'batches.title_en as batch_title_en',
            'certificate_issued.row_status'
        ]);

        $certificateIssuedBuilder->join('certificates', "certificates.id", "=", "certificate_issued.certificate_id");

        $certificateIssuedBuilder->join('batches', "batches.id", "=", "certificate_issued.batch_id");
        $certificateIssuedBuilder->orderBy('certificate_issued.id', $order);
        if (is_numeric($rowStatus)) {
            $certificateIssuedBuilder->where('certificates.row_status', $rowStatus);
        }

        if (!empty($batchIds)) {
            $certificateIssuedBuilder->whereIn('certificate_issued.batch_id', $batchIds);
        }

        if (!empty($youthIds)) {
            $certificateIssuedBuilder->whereIn('certificate_issued.youth_id', $youthIds);
        }

        if (!empty($certificateIds)) {
            $certificateIssuedBuilder->whereIn('certificate_issued.certificate_id', $certificateIds);
        }
        if (!empty($courseId)) {
            Log::info("Course Id in certificate Issued Table" . json_encode($courseId, JSON_PRETTY_PRINT));
            $certificateIssuedBuilder->whereIn('certificate_issued.course_id', $courseId);
        }
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $certificateIssued = $certificateIssuedBuilder->paginate($pageSize);
            $paginateData = (object)$certificateIssued->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $certificateIssued = $certificateIssuedBuilder->get();
        }

        $resultArray = $certificateIssued->toArray();
        $youthIds = CertificateIssued::pluck('youth_id')->unique()->toArray();
//        $certificateIds = CertificateIssued::pluck('certificate_id')->unique()->toArray();
        $youthProfiles = !empty($youthIds) ? ServiceToServiceCall::getYouthProfilesByIds($youthIds) : [];

        $indexedYouths = [];
        foreach ($youthProfiles as $item) {
            $id = $item['id'];
            $indexedYouths[$id] = $item;
        }

        foreach ($resultArray["data"] as &$item) {
            $id = $item['youth_id'];
            $youthData = $indexedYouths[$id];
            $item['youth_profile'] = $youthData;
        }

        $resultData = $resultArray['data'] ?? $resultArray;

        $response['order'] = $order;
        $response['data'] = $resultData;
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return CertificateIssued
     */
    public function getOneIssuedCertificate(int $id): CertificateIssued
    {
        /** @var CertificateIssued|Builder $certificateIssuedBuilder */
        $certificateIssuedBuilder = CertificateIssued::select([
            'certificate_issued.id',
            'certificate_issued.certificate_id',
            'certificates.title as certificate_title',
            'certificates.title_en as certificate_title_en',
            'certificates.template as certificate_template',
            'certificates.result_type as certificate_result_type',
            'certificates.issued_at',
            'certificates.language as certificate_language',
            'certificate_issued.youth_id',
            'certificate_issued.course_id',
            'courses.title as course_title',
            'courses.title_en as course_title_en',
            'training_centers.title as training_center_title',
            'training_centers.title_en as training_center_title_en',
            'certificate_issued.batch_id',
            'batches.title as batch_title',
            'batches.title_en as batch_title_en',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'certificate_issued.row_status'
        ]);

        $certificateIssuedBuilder->join('certificates', "certificates.id", "=", "certificate_issued.certificate_id");

        $certificateIssuedBuilder
            ->leftJoin('course_enrollments', "course_enrollments.certificate_issued_id", "=", "certificate_issued.id");

        $certificateIssuedBuilder->leftJoin('batches', 'batches.id', '=', 'course_enrollments.batch_id');
        $certificateIssuedBuilder->leftJoin('courses', 'courses.id', '=', 'course_enrollments.course_id');
        $certificateIssuedBuilder->leftJoin('training_centers', 'training_centers.id', '=', 'course_enrollments.training_center_id');

//        dd($certificateIssuedBuilder);

        $certificateIssuedBuilder->where('certificate_issued.id', $id);

        /** @var Certificate exam_subjects */
        return $certificateIssuedBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return CertificateIssued
     * @throws Throwable
     */
    public function store(array $data): CertificateIssued
    {
        $certificateIssued = app()->make(CertificateIssued::class);
        $certificateIssued->fill($data);
        $certificateIssued->save();
        return $certificateIssued;
    }

    public function certificateIssuedAtUpdate($certificateId)
    {
        $updateDetails = Certificate::where('id', $certificateId)->first();
        $updateDetails->issued_at = Carbon::now();
        $updateDetails->save();
    }

    public function courseEnrollmentUpdate($request, $certificateIssueData)
    {
        $updateDetails = CourseEnrollment::where('batch_id', $request['batch_id'])
            ->where('youth_id', $request['youth_id'])
            ->first();

        $updateDetails->certificate_issued_id = $certificateIssueData['id'];
        $updateDetails->save();
    }

    /**
     * @param CertificateIssued $certificateIssued
     * @param array $data
     * @return CertificateIssued
     */
    public function update(CertificateIssued $certificateIssued, array $data): CertificateIssued
    {
        $certificateIssued->fill($data);
        $certificateIssued->save();
        return $certificateIssued;
    }

    /**
     * @param CertificateIssued $certificateIssued
     * @return bool
     */
    public function destroy(CertificateIssued $certificateIssued): bool
    {
        return $certificateIssued->delete();
    }

    /**
     * @param CertificateIssued $certificateIssued
     * @return bool
     */
    public function forceDelete(CertificateIssued $certificateIssued): bool
    {
        return $certificateIssued->forceDelete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();
        $customMessage = [
            'row_status.in' => 'Order must be either ASC or DESC. [30000]',
        ];
        $rules = [
            'youth_id' => [
                'required',
                'int',
                Rule::unique('certificate_issued', 'youth_id')
                    ->where(function (\Illuminate\Database\Query\Builder $query) use ($request) {
                        return $query->where('certificate_issued.batch_id', $request->input('batch_id'))
                            ->where('certificate_issued.certificate_id', $request->input('certificate_id'));
                    }),
            ],
            'batch_id' => [
                'required',
                'int',
//                "exists:batches,id,deleted_at,NULL",
            ],
            'certificate_id' => [
                'required',
                'int',
//                "exists:certificates,id,NULL"
            ],
            'course_id' => [
                'required',
                'int',
//                "exists:certificates,id,NULL"
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
//                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }


    /**
     * @param Request $request
     * @return Validator
     */

    public function filterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        if ($request->filled('batch_id')) {
            $decodedValue = $request->get('batch_id');
            $request->offsetSet('batch_id', $this->toArray($decodedValue));
        }

        if ($request->filled('certificate_id')) {
            $decodedValue = $request->get('certificate_id');
            $request->offsetSet('certificate_id', $this->toArray($this->toArray($decodedValue)));

        }

        if ($request->filled('youth_id')) {
            $decodedValue = $request->get('youth_id');
            $request->offsetSet('youth_id', $this->toArray($this->toArray($decodedValue)));
        }
        if ($request->filled('course_id')) {
            $decodedValue = $request->get('course_id');
            $request->offsetSet('course_id', $this->toArray($this->toArray($decodedValue)));
        }

        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];
        $rules = [
            'accessor_id' => 'nullable|int|gt:0',
            'title_en' => 'nullable|max:250|min:2',
            'accessor_type' => 'nullable|max:250|min:2',
            'title' => 'nullable|max:500|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'batch_id' => [
                "nullable",
                "array"
            ],
            'batch_id.*' => [
                "required",
                "integer",
                "exists:batches,id,deleted_at,NULL"
            ],
            'certificate_id' => [
                "nullable",
                "array"
            ],
            'certificate_id.*' => [
                "nullable",
                "integer",
                "exists:certificates,id,deleted_at,NULL"
            ],
            'youth_id' => [
                "nullable",
                "array"
            ],
            'course_id' => [
                "nullable",
                "array"
            ],
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    private function toArray(int|array $data): array
    {
        if (is_array($data)) {
            return $data;
        } else {
            return [
                $data
            ];
        }
    }
}

