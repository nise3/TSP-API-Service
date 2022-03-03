<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\Subject;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SubjectService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getSubjectList(array $request, Carbon $startTime, bool $isPublicApi = false): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rowStatus = $request['row_status'] ?? "ASC";

        /** @var Subject|Builder $subjectBuilder */
        $subjectBuilder = Subject::select([
            'subjects.id',
            'subjects.title',
            'subjects.title_en',
            'subjects.row_status',
            'subjects.created_at',
            'subjects.updated_at',
            'subjects.deleted_at',
        ]);

        if(!$isPublicApi){
            $subjectBuilder->acl();
        }

        $subjectBuilder->orderBy('subjects.id', $order);

        if (!empty($titleEn)) {
            $subjectBuilder->where('subjects.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $subjectBuilder->where('subjects.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($rowStatus)) {
            $subjectBuilder->where('subjects.row_status', $rowStatus);
        }

        /** @var Collection $subjects */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $subjects = $subjectBuilder->paginate($pageSize);
            $paginateData = (object)$subjects->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $subjects = $subjectBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $subjects->toArray()['data'] ?? $subjects->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return Subject
     */
    public function getOneSubject(int $id): Subject
    {
        /** @var Subject|Builder $subjectBuilder */
        $subjectBuilder = Subject::select([
            'subjects.id',
            'subjects.title',
            'subjects.title_en',
            'subjects.row_status',
            'subjects.created_at',
            'subjects.updated_at',
            'subjects.deleted_at',
        ]);

        if (is_numeric($id)) {
            $subjectBuilder->where('subjects.id', $id);
        }

        return $subjectBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Subject
     */
    public function store(array $data): Subject
    {
        $subject = app()->make(Subject::class);
        $subject->fill($data);
        $subject->save();
        return $subject;
    }

    /**
     * @param Subject $subject
     * @param array $data
     * @return Subject
     */
    public function update(Subject $subject, array $data): Subject
    {
        $subject->fill($data);
        $subject->save();
        return $subject;
    }

    /**
     * @param Subject $subject
     * @return bool
     */
    public function destroy(Subject $subject): bool
    {
        return $subject->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $rules = [
            'title' => [
                'required',
                'string',
                'max:500',
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
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

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "int"
            ],
        ], $customMessage);
    }
}
