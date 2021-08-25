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
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getCourseList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $rowStatus=$request->query('row_status');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Course|Builder $coursesBuilder */
        $coursesBuilder = Course::select(
            [
                'courses.id as id',
                'courses.code',
                'courses.institute_id',
                'institutes.title_en as institute_title',
                'courses.title_en',
                'courses.title_bn',
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

        $coursesBuilder->join("institutes",function($join) use($rowStatus){
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if(!is_null($rowStatus)){
                $join->where('institutes.row_status',$rowStatus);
            }
        });

        $coursesBuilder->orderBy('courses.id', $order);

        if(!is_null($rowStatus)){
            $coursesBuilder->where('courses.row_status',$rowStatus);
        }

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $coursesBuilder->where('courses.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $coursesBuilder */
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
        $response['response_status'] = [
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
                'courses.id as id',
                'courses.code',
                'courses.institute_id',
                'institutes.title_en as institute_title',
                'courses.title_en',
                'courses.title_bn',
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
        $courseBuilder->join('institutes', 'courses.institute_id', '=', 'institutes.id');

        $courseBuilder->join("institutes",function($join){
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $courseBuilder->where('courses.id', '=', $id);

        /** @var Course $courseBuilder */
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

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:1000'
            ],
            'code' => [
                'required',
                'string',
                'max:191',
                'unique:courses,code,' . $id
            ],
            'course_fee' => [
                'required',
                'min:0'
            ],
            'duration' => [
                'nullable',
                'string',
                'max: 30',
            ],
            'target_group' => [
                'nullable',
                'string',
                'max: 300',
            ],
            'objectives' => [
                'nullable',
                'string',
                'max: 1000',
            ],
            'contents' => [
                'nullable',
                'string',
                'max: 300',
            ],
            'training_methodology' => [
                'nullable',
                'string',
                'max: 300',
            ],
            'evaluation_system' => [
                'nullable',
                'string',
                'max: 300',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'prerequisite' => [
                'nullable',
                'string',
                'max:300'
            ],
            'eligibility' => [
                'nullable',
                'string',
                'max:300'
            ],
            'institute_id' => [
                'required',
                'int'
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
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
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
                'institutes.title_en as institute_title',
                'courses.title_en',
                'courses.title_bn',
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
        } elseif (!empty($titleBn)) {
            $coursesBuilder->where('courses.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $coursesBuilder */
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
        $response['response_status'] = [
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
}
