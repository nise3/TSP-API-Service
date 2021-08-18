<?php

namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
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

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $coursesBuilder->where('courses.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $coursesBuilder */
        if ($paginate) {
            $courses = $coursesBuilder->paginate(10);
            $paginateData = (object)$courses->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $courses = $coursesBuilder->get();
        }

        return [
            "data" => $courses->toArray() ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
            ],
            "_page" => $page,
            "_order" => $order
        ];
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
        $courseBuilder->where('courses.id', '=', $id);

        /** @var Course $courseBuilder */
        $course = $courseBuilder->first();

        return [
            "data" => $course ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
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
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
