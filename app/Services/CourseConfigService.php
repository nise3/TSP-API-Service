<?php


namespace App\Services;


use App\Models\CourseConfig;
use App\Models\CourseSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CourseConfigService
 * @package App\Services
 */
class CourseConfigService
{

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getCourseConfigList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var CourseConfig|Builder $courseConfigBuilder */
        $courseConfigBuilder = CourseConfig::select([
            'course_configs.id as id',
            'course_configs.course_id',
            'course_configs.institute_id',
            'institutes.title_en as institute_title',
            'institutes.id as institute_id',
            'course_configs.created_at',
            'courses.title_en as course_title',
            'courses.id as course_id',
            'branches.title_en as branch_name',
            'branches.id as branch_id',
            'programmes.title_en as programme_name',
            'training_centers.title_en as training_center_name',
            'training_centers.id as training_center_id',
            'course_configs.updated_at'
        ]);

        $courseConfigBuilder->join('courses', 'course_configs.course_id', '=', 'courses.id');
        $courseConfigBuilder->join('institutes', 'course_configs.institute_id', '=', 'institutes.id');
        $courseConfigBuilder->leftJoin('programmes', 'course_configs.programme_id', '=', 'programmes.id');
        $courseConfigBuilder->leftJoin('branches', 'course_configs.branch_id', '=', 'branches.id');
        $courseConfigBuilder->leftJoin('training_centers', 'course_configs.training_center_id', '=', 'training_centers.id');

        $courseConfigBuilder->orderBy('course_configs.id', $order);


        if (!empty($titleEn)) {
            $courseConfigBuilder->where('course_configs.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $courseConfigBuilder->where('course_configs.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $courseConfigBuilder */
        if ($paginate) {
            $courseConfigs = $courseConfigBuilder->paginate(10);
            $paginateData = (object)$courseConfigs->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $courseConfigs = $courseConfigBuilder->get();
        }

        $data = [];
        foreach ($courseConfigs as $courseConfig) {
            /** @var CourseConfig $courseConfig */
            $links['read'] = route('api.v1.course-configs.read', ['id' => $courseConfig->id]);
            $links['update'] = route('api.v1.course-configs.update', ['id' => $courseConfig->id]);
            $links['delete'] = route('api.v1.course-configs.destroy', ['id' => $courseConfig->id]);
            $courseConfig['_links'] = $links;
            $data[] = $courseConfig->toArray();
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

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneCourseConfig(int $id , Carbon $startTime): array
    {
        /** @var CourseConfig|Builder $courseConfigBuilder */

        $courseConfigBuilder = CourseConfig::select([
            'course_configs.id as id',
            'course_configs.course_id',
            'course_configs.institute_id',
            'institutes.title_en as institute_title',
            'institutes.id as institute_id',
            'course_configs.created_at',
            'courses.title_en as course_title',
            'courses.id as course_id',
            'branches.title_en as branch_name',
            'branches.id as branch_id',
            'programmes.title_en as programme_name',
            'training_centers.title_en as training_center_name',
            'training_centers.id as training_center_id',
            'course_configs.updated_at'
        ]);

        $courseConfigBuilder->join('courses', 'course_configs.course_id', '=', 'courses.id');
        $courseConfigBuilder->join('institutes', 'course_configs.institute_id', '=', 'institutes.id');
        $courseConfigBuilder->leftJoin('programmes', 'course_configs.programme_id', '=', 'programmes.id');
        $courseConfigBuilder->leftJoin('branches', 'course_configs.branch_id', '=', 'branches.id');
        $courseConfigBuilder->leftJoin('training_centers', 'course_configs.training_center_id', '=', 'training_centers.id');
        $courseConfigBuilder->where('course_configs.id', $id);

        /** @var CourseConfig $instituteBuilder */
        $courseConfig = $courseConfigBuilder->first();

        if ($courseConfig) {
            $courseConfig->load('courseSessions');
        }

        $links = [];
        if ($courseConfig) {
            $links['update'] = route('api.v1.course-configs.update', ['id' => $id]);
            $links['delete'] = route('api.v1.course-configs.destroy', ['id' => $id]);
        }
        return [
            "data" => $courseConfig ?: null,
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
     * @return CourseConfig
     */
    public function store(array $data): CourseConfig
    {
        $courseConfig = new CourseConfig();
        $courseConfig->fill($data);
        $courseConfig->save();


        return $courseConfig;
    }

    /**
     * @param CourseConfig $courseConfig
     * @param array $data
     * @return CourseConfig
     */
    public function update(CourseConfig $courseConfig, array $data): CourseConfig
    {
        $courseConfig->fill($data);
        $courseConfig->save();

        return $courseConfig;

    }

    /**
     * @param CourseConfig $courseConfig
     * @return CourseConfig
     */
    public function destroy(CourseConfig $courseConfig): CourseConfig
    {
        $courseConfig->row_status = CourseConfig::ROW_STATUS_DELETED;
        $courseConfig->save();
        $courseConfig->delete();

        foreach ($courseConfig->courseSessions() as $courseSession) {
            $courseSession->row_status = CourseSession::ROW_STATUS_DELETED;
        }

        return $courseConfig;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = Null):  \Illuminate\Contracts\Validation\Validator
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
            ],
            'row_status' => [
                'required_if:' . $id . ',==,null',
                Rule::in([CourseConfig::ROW_STATUS_ACTIVE, CourseConfig::ROW_STATUS_INACTIVE]),
            ],
        ];

        $messages = [
            'course_sessions.*.session_name_bn.regex' => "Session Name(Bangla) is required in Bangla",
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

}
