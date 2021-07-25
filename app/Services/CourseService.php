<?php


namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CourseService
 * @package App\Services
 */
class CourseService
{

    /**
     * @param Request $request
     * @return array
     */
    public function getCourseList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $courses = Course::select(
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
            ]
        )
            ->join('institutes', 'courses.institute_id', '=', 'institutes.id');

        if (!empty($titleEn)) {
            $courses->where('courses.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $courses->where('courses.title_bn', 'like', '%' . $titleBn . '%');
        }


        if ($paginate) {
            $courses = $courses->paginate(10);
            $paginate_data = (object)$courses->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $courses = $courses->get();
        }

        $data = [];

        foreach ($courses as $course) {
            $_links['read'] = route('api.v1.courses.read', ['id' => $course->id]);
            $_links['update'] = route('api.v1.courses.update', ['id' => $course->id]);
            $_links['delete'] = route('api.v1.courses.destroy', ['id' => $course->id]);
            $course['_links'] = $_links;
            $data[] = $course->toArray();
        }
        $response = [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => [
                'paginate' => $paginate_link,
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

        return $response;

    }

    /**
     * @param $id
     * @return array
     */
    public function getOneCourse($id)
    {
        $startTime = Carbon::now();
        $course = Course::select(
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
            ]
        )
            ->join('institutes', 'courses.institute_id', '=', 'institutes.id')
            ->where('courses.row_status', '=', Course::ROW_STATUS_ACTIVE)
            ->where('courses.id', '=', $id);
        $course = $course->first();

        $links = [];
        if (!empty($course)) {
            $links['update'] = route('api.v1.courses.update', ['id' => $id]);
            $links['delete'] = route('api.v1.courses.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $course ? $course : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
        return $response;
    }

    /**
     * @param array $data
     * @return Course
     */
    public function store(array $data): Course
    {
        $filename = null;
        if (!empty($data['cover_image'])) {
            $filename = FileHandler::storePhoto($data['cover_image'], 'course');
        }
        if ($filename) {
            $data['cover_image'] = 'course/' . $filename;
        } else {
            $data['cover_image'] = Course::DEFAULT_COVER_IMAGE;
        }

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

        return $course;
    }

    /**
     * @param Request $request
     * return use Illuminate\Support\Facades\Validator;
     */
    public function validator(Request $request, $id = null): \Illuminate\Contracts\Validation\Validator
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
                'max:191'
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
                'file',
                'mimes:jpg,bmp,png,jpeg,svg',
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

}
