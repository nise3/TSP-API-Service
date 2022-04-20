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

class CertificateTypeService
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
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var Certificate|Builder $CertificateBuilder */
        $CertificateBuilder = Certificate::select([
            'certificates.id',
            'certificates.title',
            'certificates.title_en'
        ]);

        $CertificateBuilder->orderBy('certificates.id', $order);

        if (is_numeric($rowStatus)) {
            $CertificateBuilder->where('certificates.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $CertificateBuilder->where('certificates.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $CertificateBuilder->where('certificates.title', 'like', '%' . $title . '%');
        }
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $certificateTypes = $CertificateBuilder->paginate($pageSize);
            $paginateData = (object)$certificateTypes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $certificateTypes = $CertificateBuilder->get();
        }

        $response['order'] = $order;
//        $response['data'] = $certificate->toArray()['data'] ?? $certificate->toArray();
        $response['data'] = [
            BaseModel::CERTIFICATE_TYPE_A,
            BaseModel::CERTIFICATE_TYPE_B
        ];
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
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

