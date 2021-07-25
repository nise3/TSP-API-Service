<?php


namespace App\Services;


use App\Models\CourseConfig;
use App\Models\CourseSession;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CourseConfigService
{
    public function getCourseConfigList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        $courseConfigs = CourseConfig::select([
            'course_configs.id as id',
            'course_configs.course_id',
            'course_configs.institute_id',
            'institutes.title_en as institute_title',
            'course_configs.created_at',
            'courses.title_en as course_title',
            'branches.title_en as branch_name',
            'programmes.title_en as programme_name',
            'training_centers.title_en as training_center_name',
            'course_configs.updated_at'
        ]);


        $courseConfigs->join('courses', 'course_configs.course_id', '=', 'courses.id');
        $courseConfigs->join('institutes', 'course_configs.institute_id', '=', 'institutes.id');
        $courseConfigs->leftJoin('programmes', 'course_configs.programme_id', '=', 'programmes.id');
        $courseConfigs->leftJoin('branches', 'course_configs.branch_id', '=', 'branches.id');
        $courseConfigs->leftJoin('training_centers', 'course_configs.training_center_id', '=', 'training_centers.id');

        $courseConfigs->orderBy('course_configs.id', $order);


        if (!empty($titleEn)) {
            $courseConfigs->where('course_configs.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $courseConfigs->where('course_configs.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $courseConfigs = $courseConfigs->paginate(10);
            $paginate_data = (object)$courseConfigs->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $courseConfigs = $courseConfigs->get();
        }

        $data = [];
        foreach ($courseConfigs as $courseConfig) {
            $_links['read'] = route('api.v1.course-configs.read', ['id' => $courseConfig->id]);
            $_links['update'] = route('api.v1.course-configs.update', ['id' => $courseConfig->id]);
            $_links['delete'] = route('api.v1.course-configs.destroy', ['id' => $courseConfig->id]);
            $courseConfig['_links'] = $_links;
            $data[] = $courseConfig->toArray();
        }

        return [
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

                "search" => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.course-configs.get-list')

                ],

            ],

            "_page" => $page,
            "_order" => $order
        ];
    }

    public function getOneCourseConfig($id): array
    {
        $startTime = Carbon::now();
        $courseConfig = CourseConfig::select([
            'course_configs.id as id',
            'course_configs.course_id',
            'course_configs.institute_id',
            'institutes.title_en as institute_title',
            'course_configs.created_at',
            'courses.title_en as course_title',
            'branches.title_en as branch_name',
            'programmes.title_en as programme_name',
            'training_centers.title_en as training_center_name',
            'course_configs.updated_at'
        ]);
        $courseConfig->join('courses', 'course_configs.course_id', '=', 'courses.id');
        $courseConfig->join('institutes', 'course_configs.institute_id', '=', 'institutes.id');
        $courseConfig->leftJoin('programmes', 'course_configs.programme_id', '=', 'programmes.id');
        $courseConfig->leftJoin('branches', 'course_configs.branch_id', '=', 'branches.id');
        $courseConfig->leftJoin('training_centers', 'course_configs.training_center_id', '=', 'training_centers.id');
        $courseConfig->where('course_configs.id', $id);

        $courseConfig = $courseConfig->first();
        if (!empty($courseConfig)) {
            $courseConfig->load('courseSessions');
        }

        $links = [];
        if (!empty($courseConfig)) {
            $links['update'] = route('api.v1.course-configs.update', ['id' => $id]);
            $links['delete'] = route('api.v1.course-configs.destroy', ['id' => $id]);
        }
        return [
            "data" => $courseConfig ?: null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];

    }

    /**
     * @param array $data
     * @return CourseConfig
     */
    public function store(array $data): CourseConfig
    {
        $courseConfig = new CourseConfig();
        $courseConfig->fill($data);
        $courseConfig->save();

        foreach ($data['course_sessions'] as $session) {
            $session['course_id'] = $data['course_id'];
            $courseSessions[] = $session;
            $courseConfig->courseSessions()->create($session);
        }
        return $courseConfig;
    }

    public function update(CourseConfig $courseConfig, array $data): CourseConfig
    {
        $courseConfig->fill($data);
        $courseConfig->save();

        foreach ($data['course_sessions'] as $session) {
            $session['course_id'] = $data['course_id'];
            $courseSessions[] = $session;

            if (empty($session['id'])) {
                $courseConfig->courseSessions()->create($session);
                continue;
            }

            $courseSession = CourseSession::findOrFail($session['id']);

            if (!empty($session['delete']) && $session['delete'] == 1) {
                $courseSession->delete();
            } else {
                $courseSession->update($session);
            }
        }

        return $courseConfig;

    }

    public function destroy(CourseConfig $courseConfig): CourseConfig
    {
        $courseConfig->row_status = CourseConfig::ROW_STATUS_DELETED;
        $courseConfig->save();

//        soft delete corresponding course sessions of this course configuration
        foreach ($courseConfig->courseSessions() as $courseSession) {
            $courseSession->row_status = CourseSession::ROW_STATUS_DELETED;
        }

        return $courseConfig;
    }


    /**
     * @param Request $request
     * @param null $id
     * @return Validator
     */
    public function validator(Request $request): Validator
    {
        $rules = [
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id',
            ],
            'course_id' => [
                'required',
                'int',
                'exists:courses,id'
            ],
            'training_center_id' => [
                'nullable',
                'int'
            ],
            'programme_id' => [
                'nullable',
                'int'
            ],
            'branch_id' => [
                'nullable',
                'int'
            ],
            'course_sessions' => [
                "required",
                "array",
                "min:1"
            ],
            'course_sessions.*.session_name_en' => [
                'required',
                'string',
                'max:30'
            ],
            'course_sessions.*.session_name_bn' => [
                'required',
                'string',
                'max:30',
                'regex:/^[\x{0980}-\x{09FF}\s\-\*!@#%\+=\._\^\'()]*$/u',
            ],
            'course_sessions.*.number_of_batches' => [
                'required',
                'int'
            ],
            'course_sessions.*.application_start_date' => [
                'required',
                'date'
            ],
            'course_sessions.*.application_end_date' => [
                'required',
                'date'
            ],
            'course_sessions.*.course_start_date' => [
                'required',
                'date'
            ],
            'course_sessions.*.max_seat_available' => [
                'required',
                'int'
            ],
            'ethnic' => [
                'boolean',
                'nullable'
            ],
            'freedom_fighter' => [
                'nullable',
                'boolean'
            ],
            'disable_status' => [
                'nullable',
                'boolean'
            ],
            'ssc' => [
                'nullable',
                'boolean'
            ],
            'hsc' => [
                'nullable',
                'boolean',
            ],
            'honors' => [
                'nullable',
                'boolean',
            ],
            'masters' => [
                'nullable',
                'boolean',
            ],
            'occupation' => [
                'nullable',
                'boolean',
            ],
            'guardian' => [
                'nullable',
                'boolean',
            ]
        ];

        $messages = [
            'course_sessions.*.session_name_bn.regex' => "Session Name(Bangla) is required in Bangla",
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
    }

}
