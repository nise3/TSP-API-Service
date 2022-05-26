<?php
namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\CertificateTemplates;
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

class CertificateTemplatesService
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


        /** @var CertificateTemplates|Builder $certificateBuilder */
        $certificateBuilder = CertificateTemplates::select([
            'certificate_templates.id',
            'certificate_templates.title',
            'certificate_templates.title_en',
            'certificate_templates.result_type',
            'certificate_templates.accessor_type',
            'certificate_templates.accessor_id',
            'certificate_templates.language',
            'certificate_templates.row_status',
            'certificate_templates.created_at',
            'certificate_templates.updated_at',
            'certificate_templates.issued_at'

        ])->acl();

        $certificateBuilder->orderBy('certificate_templates.id', $order);

        if (is_numeric($rowStatus)) {
            $certificateBuilder->where('certificate_templates.row_status', $rowStatus);
        }

        if(is_numeric($resultType)) {
            $certificateBuilder->where('certificate_templates.result_type', $resultType);
        }
        if (!empty($titleEn)) {
            $certificateBuilder->where('certificate_templates.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $certificateBuilder->where('certificate_templates.title', 'like', '%' . $title . '%');
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
     * @return CertificateTemplates
     */
    public function getOneCertificate(int $id): CertificateTemplates
    {
        /** @var CertificateTemplates|Builder $CertificateBuilder */
        $CertificateBuilder = CertificateTemplates::select([
            'certificate_templates.id',
            'certificate_templates.title',
            'certificate_templates.title_en',
            'certificate_templates.template',
            'certificate_templates.result_type',
            'certificate_templates.accessor_type',
            'certificate_templates.accessor_id',
            'certificate_templates.language',
            'certificate_templates.row_status',
            'certificate_templates.created_at',
            'certificate_templates.updated_at',
            'certificate_templates.issued_at'
        ]);

        $CertificateBuilder->where('certificate_templates.id', $id);

        /** @var CertificateTemplates exam_subjects */
        return $CertificateBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return CertificateTemplates
     * @throws Throwable
     */
    public function store(array $data): CertificateTemplates
    {
        $certificateTemplate = app()->make(CertificateTemplates::class);
        $certificateTemplate->fill($data);
        $certificateTemplate->save();

        return $certificateTemplate;
    }

    /**
     * @param CertificateTemplates $certificate
     * @param array $data
     * @return CertificateTemplates
     */
    public function update(CertificateTemplates $certificate, array $data): CertificateTemplates
    {
        $certificate->fill($data);
        $certificate->save();
        return $certificate;
    }

    /**
     * @param CertificateTemplates $certificate
     * @return bool
     */
    public function destroy(CertificateTemplates $certificate): bool
    {
        return $certificate->delete();
    }

    /**
     * @param CertificateTemplates $certificate
     * @return bool
     */
    public function forceDelete(CertificateTemplates $certificate): bool
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
                'max:500',
                Rule::unique('certificate_templates', 'title')
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:250',
                Rule::unique('certificate_templates', 'title_en')
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

