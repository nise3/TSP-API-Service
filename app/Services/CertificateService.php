<?php
namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Certificate;
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

class CertificateService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $resultType = $request['result_type'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Certificate|Builder $certificateBuilder */
        $certificateBuilder = Certificate::select([
            'certificates.id',
            'certificates.title',
            'certificates.title_en',
            'certificates.template',
            'certificates.result_type',
            'certificates.accessor_type',
            'certificates.accessor_id',
            'certificates.language',
            'certificates.row_status',
            'certificates.created_at',
            'certificates.updated_at',
            'certificates.issued_at'

        ])->acl();

        $certificateBuilder->orderBy('certificates.id', $order);

        if (is_numeric($rowStatus)) {
            $certificateBuilder->where('certificates.row_status', $rowStatus);
        }

        if(is_numeric($resultType)) {
            $certificateBuilder->where('certificates.result_type', $resultType);
        }
        if (!empty($titleEn)) {
            $certificateBuilder->where('certificates.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $certificateBuilder->where('certificates.title', 'like', '%' . $title . '%');
        }
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $certificate = $certificateBuilder->paginate($pageSize);
            $paginateData = (object)$certificate->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $certificate = $certificateBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $certificate->toArray()['data'] ?? $certificate->toArray();
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
    public function getOneCertificate(int $id): Certificate
    {
        /** @var Certificate|Builder $CertificateBuilder */
        $CertificateBuilder = Certificate::select([
            'certificates.id',
            'certificates.title',
            'certificates.title_en',
            'certificates.template',
            'certificates.result_type',
            'certificates.accessor_type',
            'certificates.accessor_id',
            'certificates.language',
            'certificates.row_status',
            'certificates.created_at',
            'certificates.updated_at',
            'certificates.issued_at'
        ]);

        $CertificateBuilder->where('certificates.id', $id);

        /** @var Certificate exam_subjects */
        return $CertificateBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Certificate
     * @throws Throwable
     */
    public function store(array $data): Certificate
    {
        $certificateTemplate = app()->make(Certificate::class);
        $certificateTemplate->fill($data);
        $certificateTemplate->save();

        return $certificateTemplate;
    }

    /**
     * @param Certificate $certificate
     * @param array $data
     * @return Certificate
     */
    public function update(Certificate $certificate, array $data): Certificate
    {
        $certificate->fill($data);
        $certificate->save();
        return $certificate;
    }

    /**
     * @param Certificate $certificate
     * @return bool
     */
    public function destroy(Certificate $certificate): bool
    {
        return $certificate->delete();
    }

    /**
     * @param Certificate $certificate
     * @return bool
     */
    public function forceDelete(Certificate $certificate): bool
    {
        return $certificate->forceDelete();
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
                'required',
                'string',
                'max:500'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'template' => [
                'nullable',
                'string'
            ],
            'result_type' => [
                'required',
                'int',
                Rule::in([
                    BaseModel::CERTIFICATE_COMPETENT,
                    BaseModel::CERTIFICATE_NOT_COMPETENT,
                    BaseModel::CERTIFICATE_GRADING,
                    BaseModel::CERTIFICATE_MARKS
                ])
            ],
            'language' => [
                'required',
                'int',
                Rule::in([
                    BaseModel::CERTIFICATE_BD,
                    BaseModel::CERTIFICATE_EN,
                ])
            ],
            'accessor_type' => [
                'required',
                'string',
                'max:250',
                Rule::in(BaseModel::ACCESSOR_TYPES)
            ],
            'accessor_id' => [
                'required',
                'int',
                'min:1'
            ],
            'issued_at' => [
                'nullable',
                'string'
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
            'result_type' => 'nullable',
            'language' => 'int|gt:0',
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

