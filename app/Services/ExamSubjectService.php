<?php
namespace App\Services;

use App\Facade\ServiceToServiceCall;
use App\Models\BaseModel;
use App\Models\ExamSubject;
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

class ExamSubjectService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['$titleEn'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";


        /** @var ExamSubject|Builder $trainerBuilder */
        $ExamSubjectBuilder = ExamSubject::select([
            'title',
            'title_en',
            'accessor_type',
            'accessor_id',
            'exam_subjects.row_status',
            'exam_subjects.created_at',
            'exam_subjects.updated_at',
            'exam_subjects.deleted_at',
        ]);

        $ExamSubjectBuilder->orderBy('exam_subjects.id', $order);

        if (is_numeric($rowStatus)) {
            $ExamSubjectBuilder->where('exam_subjects.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $ExamSubjectBuilder->where('exam_subjects.titleEn', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $ExamSubjectBuilder->where('trainers.title', 'like', '%' . $title . '%');
        }


        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $trainers = $ExamSubjectBuilder->paginate($pageSize);
            $paginateData = (object)$trainers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $ExamSubject = $ExamSubjectBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $ExamSubject->toArray()['data'] ?? $ExamSubject->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return ExamSubject
     */
    public function getOneExamSubject(int $id): ExamSubject
    {
        /** @var ExamSubject|Builder $trainerBuilder */
        $ExamSubjectBuilder = ExamSubject::select([
            'exam_subjects.id',
            'title',
            'title_en',
            'accessor_type',
            'accessor_id',
            'exam_subjects.row_status',
            'exam_subjects.created_at',
            'exam_subjects.updated_at',
            'exam_subjects.deleted_at',
        ]);

        $ExamSubjectBuilder->where('exam_subjects.id', $id);

        /** @var ExamSubject exam_subjects */
        return $ExamSubjectBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return ExamSubject
     * @throws Throwable
     */
    public function store(array $data): ExamSubject
    {
        $subject = app()->make(ExamSubject::class);
        $subject->fill($data);
        $subject->save();
        return $subject;
    }

    /**
     * @param ExamSubject $trainer
     * @param array $data
     * @return ExamSubject
     */
    public function update(ExamSubject $trainer, array $data): ExamSubject
    {
        $trainer->fill($data);
        $trainer->save();
        return $trainer;
    }

    /**
     * @param ExamSubject $trainer
     * @return bool
     */
    public function destroy(ExamSubject $trainer): bool
    {
        return $trainer->delete();
    }

    /**
     * @param ExamSubject $exam_subject
     * @return bool
     */
    public function forceDelete(ExamSubject $exam_subject): bool
    {
        return $exam_subject->forceDelete();
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
            'accessor_type' => [
                'required',
                'string',
                'max:250'
            ],
            'accessor_id' => [
                'required',
                'string',
                'max:250'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => [
                'nullable',
                'integer',
            ],
            'updated_by' => [
                'nullable',
                'integer',
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }

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
