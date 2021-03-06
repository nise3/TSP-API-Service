<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\BatchCertificateTemplates;
use App\Models\CertificateTemplates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BatchCertificateTemplateService
 * @package App\Services
 */
class BatchCertificateTemplateService
{

    /**
     * @param array $request
     * @param \Carbon\Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $certificateTemplateId = $request['certificate_template_id'] ?? "";
        $batchid = $request['batch_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var BatchCertificateTemplates|Builder $batchCertificateTemplateBuilder */
        $batchCertificateTemplateBuilder = BatchCertificateTemplates::select([
            'batch_certificate_templates.certificate_template_id',
            'certificate_templates.result_type',
            'batch_certificate_templates.batch_id',
            'certificate_templates.title_en as certificate_templates_title_en',
            'certificate_templates.title as certificate_templates_title'

        ])->acl();

        $batchCertificateTemplateBuilder->join("certificate_templates", function ($join) use ($rowStatus) {
            $join->on('batch_certificate_templates.certificate_template_id', '=', 'certificate_templates.id');
        });

//        $batchCertificateTemplateBuilder->orderBy('batch_certificate_templates.id', $order);

        if (is_numeric($rowStatus)) {
            $batchCertificateTemplateBuilder->where('batch_certificate_templates.row_status', $rowStatus);
        }

        if (!empty($certificateTemplateId)) {
            $batchCertificateTemplateBuilder->where('batch_certificate_templates.certificate_template_id', $certificateTemplateId);
        }
        if (!empty($batchid)) {
            $batchCertificateTemplateBuilder->where('batch_certificate_templates.batch_id', $batchid);
        }
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $certificateTemplate = $batchCertificateTemplateBuilder->paginate($pageSize);
            $paginateData = (object)$certificateTemplate->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $certificateTemplate = $batchCertificateTemplateBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $certificateTemplate->toArray()['data'] ?? $certificateTemplate->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getListByBatchId(array $request, int $id): array
    {
        $startTime = Carbon::now();
        $rowStatus = $request['row_status'] ?? "";

            /** @var BatchCertificateTemplateService|Builder $batchCertificateBuilder */
        $batchCertificateBuilder = BatchCertificateTemplates::select([
//            'batch_certificate_templates.id',
            'batch_certificate_templates.certificate_template_id',
            'certificate_templates.result_type',
            'batch_certificate_templates.batch_id',
            'certificate_templates.title_en as certificate_templates_title',
            'certificate_templates.title as certificate_templates_title'
        ])
        ->acl();

        $batchCertificateBuilder->join("certificate_templates", function ($join) use ($rowStatus) {
            $join->on('batch_certificate_templates.certificate_template_id', '=', 'certificate_templates.id');
                //->whereNull('batch_certificate_templates.deleted_at');
        });

        $batchTemplate = $batchCertificateBuilder->where('batch_certificate_templates.batch_id', $id)->get();
        $response['data'] = $batchTemplate->toArray()['data'] ?? $batchTemplate->toArray();


        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getListByBatchIds(array $request, array $ids): array
    {
        $startTime = Carbon::now();
        $rowStatus = $request['row_status'] ?? "";

        /** @var BatchCertificateTemplateService|Builder $batchCertificateBuilder */
        $batchCertificateBuilder = BatchCertificateTemplates::select([
//            'batch_certificate_templates.id',
            'batch_certificate_templates.certificate_template_id',
            'certificate_templates.result_type',
            'batch_certificate_templates.batch_id',
            'certificate_templates.title_en as certificate_templates_title_en',
            'certificate_templates.title as certificate_templates_title'
        ])
            ->acl();

        $batchCertificateBuilder->join("certificate_templates", function ($join) use ($rowStatus) {
            $join->on('batch_certificate_templates.certificate_template_id', '=', 'certificate_templates.id');
            //->whereNull('batch_certificate_templates.deleted_at');
        });

        $batchTemplate = $batchCertificateBuilder->whereIn('batch_certificate_templates.batch_id', $ids)->get();
        $response['data'] = $batchTemplate->toArray()['data'] ?? $batchTemplate->toArray();


        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        return Validator::make($request->all(), [
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'certificate_template_id' => 'nullable|int',
            'batch_id' => 'nullable|int',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}

