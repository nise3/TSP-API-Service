<?php


namespace App\Services;


use App\Exceptions\HttpErrorException;
use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\BatchCertificateTemplates;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseResultConfig;
use App\Models\Exam;
use App\Models\ExamQuestionBank;
use App\Models\ExamSection;
use App\Models\ExamType;
use App\Models\Result;
use App\Models\ResultSummary;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use App\Models\User;
use App\Models\YouthExam;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class BatchCertificateTemplateService
 * @package App\Services
 */
class BatchCertificateTemplateService
{

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
            'batch_certificate_templates.id',
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

