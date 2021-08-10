<?php

namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
                'courses.title_en',
                'courses.title_bn',
                'courses.duration',
                'courses.code',
                'courses.course_fee',
                'courses.target_group',
                'courses.contents',
                'courses.objects',
                'courses.training_methodology',
                'courses.evaluation_system',
                'courses.created_at',
                'courses.updated_at',
                'institutes.title_en as institute_title',
                'institutes.id as institute_id',
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

        $data = [];
        foreach ($courses as $course) {
            /** @var Course $course */
            $links['read'] = route('api.v1.courses.read', ['id' => $course->id]);
            $links['update'] = route('api.v1.courses.update', ['id' => $course->id]);
            $links['delete'] = route('api.v1.courses.destroy', ['id' => $course->id]);
            $course['_links'] = $links;
            $data[] = $course->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => [
                'paginate' => $paginateLink,
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.courses.get-list')
                ],
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
                'courses.title_en',
                'courses.title_bn',
                'courses.duration',
                'courses.code',
                'courses.course_fee',
                'courses.target_group',
                'courses.contents',
                'courses.objects',
                'courses.training_methodology',
                'courses.evaluation_system',
                'courses.created_at',
                'courses.updated_at',
                'institutes.title_en as institute_title',
                'institutes.id as institute_id',
            ]
        );
        $courseBuilder->join('institutes', 'courses.institute_id', '=', 'institutes.id');
        $courseBuilder->where('courses.id', '=', $id);

        /** @var Course $courseBuilder */
        $course = $courseBuilder->first();

        $links = [];
        if ($course) {
            $links['update'] = route('api.v1.courses.update', ['id' => $id]);
            $links['delete'] = route('api.v1.courses.destroy', ['id' => $id]);
        }

        return [
            "data" => $course ? $course : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
            ],
            "_links" => $links,
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
     * @return Course
     */
    public function destroy(Course $course): Course
    {
        $course->row_status = 99;
        $course->save();
        $course->delete();
        return $course;
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
            'objects' => [
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
