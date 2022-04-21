<?php
namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Certificate;
use App\Models\CertificateIssued;
use App\Models\RplSubject;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
//        $titleEn = $request['title_en'] ?? "";
//        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var CertificateIssued|Builder $CertificateIssuedBuilder */
        $CertificateIssuedBuilder =  CertificateIssued::select([
            'certificate_issued.certificate_id',
            'certificate_issued.youth_id',
            'certificate_issued.batch_id',
            'certificate_issued.row_status'
        ]);

        $CertificateIssuedBuilder->orderBy('certificate_issued.id', $order);

        if (is_numeric($rowStatus)) {
            $CertificateIssuedBuilder->where('certificates.row_status', $rowStatus);
        }

//        if (!empty($titleEn)) {
//            $CertificateBuilder->where('certificates.title_en', 'like', '%' . $titleEn . '%');
//        }
//        if (!empty($title)) {
//            $CertificateBuilder->where('certificates.title', 'like', '%' . $title . '%');
//        }
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $certificateIssued = $CertificateIssuedBuilder->paginate($pageSize);
            $paginateData = (object)$certificateIssued->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $certificateIssued = $CertificateIssuedBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $certificateIssued->toArray()['data'] ?? $certificateIssued->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return Certificate
     */
    public function getOneIssuedCertificate(int $id): CertificateIssued
    {
        /** @var Certificate|Builder $CertificateIssuedBuilder */
        $CertificateIssuedBuilder = CertificateIssued::select([
            'certificate_issued.id',
            'certificate_issued.certificate_id',
            'certificate_issued.youth_id',
            'certificate_issued.batch_id',
            'certificate_issued.row_status',
            'certificate_issued.created_at',
            'certificate_issued.updated_at'
        ]);
        $CertificateIssuedBuilder->where('certificate_issued.id', $id);
        /** @var Certificate exam_subjects */
        return $CertificateIssuedBuilder->firstOrFail();
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

    /**
     * @param CertificateIssued $certificateIssued
     * @param array $data
     * @return CertificateIssued
     */
    public function update(CertificateIssued $certificateIssued, array $data): Certificate
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
            'title' => [
//                'required',
                'string',
                'max:500'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'template' => [
                'string'
            ],
            'accessor_type' => [
//                'required',
                'string',
                'max:250',
                Rule::in(BaseModel::EXAM_ACCESSOR_TYPES)
            ],
            'accessor_id' => [
//                'required',
                'int',
                'min:1'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
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
}

