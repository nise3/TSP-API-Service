<?php

namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

/**
 * Class CourseService
 * @package App\Services
 */
class CourseService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getCourseList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title_bn'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.title_en',
                'courses.title',
                'courses.institute_id',
                'institutes.title_en as institute_title_en',
                'institutes.title as institute_title_bn',
                'courses.course_fee',
                'courses.duration',
                'courses.description',
                'courses.description_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.prerequisite',
                'courses.prerequisite_en',
                'courses.eligibility',
                'courses.eligibility_en',
                'courses.cover_image',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
            ]
        );

        $coursesBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $coursesBuilder->orderBy('courses.id', $order);

        if (is_numeric($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $titleBn . '%');
        }

        if (is_numeric($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        /** @var Collection $courses */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $courses = $coursesBuilder->paginate($pageSize);
            $paginateData = (object)$courses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courses = $coursesBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneCourse(int $id, Carbon $startTime): array
    {
        /** @var Course|Builder $courseBuilder */
        $courseBuilder = Course::select(
            [
                'courses.id',
                'courses.code',
                'courses.title_en',
                'courses.title',
                'courses.institute_id',
                'institutes.title_en as institute_title_en',
                'institutes.title as institute_title_bn',
                'courses.course_fee',
                'courses.duration',
                'courses.description',
                'courses.description_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.objectives',
                'courses.objectives_en',
                'courses.contents',
                'courses.contents_en',
                'courses.training_methodology',
                'courses.training_methodology_en',
                'courses.evaluation_system',
                'courses.evaluation_system_en',
                'courses.prerequisite',
                'courses.prerequisite_en',
                'courses.eligibility',
                'courses.eligibility_en',
                'courses.cover_image',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
            ]
        );

        $courseBuilder->join("institutes", function ($join) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $courseBuilder->where('courses.id', '=', $id);

        /** @var Course $course */
        $course = $courseBuilder->first();

        return [
            "data" => $course ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param array $data
     * @return Course
     */
    public function store(array $data): Course
    {
        $course = new Course();
        $course->fill($data);
        $course->save();
        return $course;
    }

    /**
     * @param Course $course
     * @param array $data
     * @return Course
     */
    public function update(Course $course, array $data): Course
    {
        $course->fill($data);
        $course->save();
        return $course;
    }

    /**
     * @param Course $course
     * @return bool
     */
    public function destroy(Course $course): bool
    {
        return $course->delete();
    }


    public function getCourseTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::onlyTrashed()->select(
            [
                'courses.id as id',
                'courses.code',
                'courses.institute_id',
                'institutes.title_en as institute_title_en',
                'institutes.title as institute_title_bn',
                'courses.title_en',
                'courses.title',
                'courses.course_fee',
                'courses.duration',
                'courses.description',
                'courses.target_group',
                'courses.objectives',
                'courses.contents',
                'courses.training_methodology',
                'courses.evaluation_system',
                'courses.prerequisite',
                'courses.eligibility',
                'courses.cover_image',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
            ]
        );
        $coursesBuilder->join('institutes', 'courses.institute_id', '=', 'institutes.id');

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $courses */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $courses = $coursesBuilder->paginate($limit);
            $paginateData = (object)$courses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courses = $coursesBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    public function restore(Course $course): bool
    {
        return $course->restore();
    }

    public function forceDelete(Course $courses): bool
    {
        return $courses->forceDelete();
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be either 1 or 0'
            ]
        ];
        $rules = [
            'title_en' => [
                'nullable',
                'string',
                'max:255',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            'code' => [
                'required',
                'string',
                'max:150',
                'unique:courses,code,' . $id
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id'
            ],
            'course_fee' => [
                'required',
                'numeric',
                'min:0'
            ],
            'duration' => [
                'nullable',
                'numeric',
                'max: 14',
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'description_en' => [
                'nullable',
                'string'
            ],
            'target_group_en' => [
                'nullable',
                'string',
                'max: 500',
            ],
            'target_group' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'objectives' => [
                'nullable',
                'string'
            ],
            'objectives_en' => [
                'nullable',
                'string'
            ],
            'contents' => [
                'nullable',
                'string'
            ],
            'contents_en' => [
                'nullable',
                'string'
            ],
            'training_methodology' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'training_methodology_en' => [
                'nullable',
                'string',
                'max: 600',
            ],
            'evaluation_system' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'evaluation_system_en' => [
                'nullable',
                'string',
                'max: 500',
            ],
            'prerequisite' => [
                'nullable',
                'string'
            ],
            'prerequisite_en' => [
                'nullable',
                'string'
            ],
            'eligibility' => [
                'nullable',
                'string',
            ],
            'eligibility_en' => [
                'nullable',
                'string',
            ],
            'cover_image' => [
                'nullable',
                'string',
                'max:191',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|max:500|min:2',
            'title' => 'nullable|max:1000|min:2',
            'page_size' => 'numeric|gt:0',
            'page' => 'numeric|gt:0',
            'institute_id' => 'numeric|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
