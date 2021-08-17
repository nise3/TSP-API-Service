<?php


namespace App\Services;


use App\Models\Batche;
use App\Models\CourseSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BatcheService
 * @package App\Services
 */
class BatcheService
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

        /** @var Batche|Builder $batchBuilder */
        $batchBuilder = Batche::select([
            'batches.id as id',
            'batches.course_id',
            'courses.title_en as course_title',
            'batches.institute_id',
            'institutes.title_en as institute_title',
            'institutes.id as institute_id',
            'branches.id as branch_id',
            'branches.title_en as branch_name',
            'batches.programme_id',
            'programmes.title_en as programme_name',
            'batches.number_of_vacancies',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_vacancies',
            'training_centers.id as training_center_id',
            'training_centers.title_en as training_center_name',
            'batches.in_ethnic_group',
            'batches.is_freedom_fighter',
            'batches.disability_status',
            'batches.ssc_passing_status',
            'batches.hsc_passing_status',
            'batches.honors_passing_status',
            'batches.masters_passing_status',
            'batches.is_occupation_needed',
            'batches.is_guardian_info_needed',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at'
        ]);

        $batchBuilder->join('courses', 'batches.course_id', '=', 'courses.id');
        $batchBuilder->join('institutes', 'batches.institute_id', '=', 'institutes.id');
        $batchBuilder->leftJoin('programmes', 'batches.programme_id', '=', 'programmes.id');
        $batchBuilder->leftJoin('branches', 'batches.branch_id', '=', 'branches.id');
        $batchBuilder->leftJoin('training_centers', 'batches.training_center_id', '=', 'training_centers.id');

        $batchBuilder->orderBy('batches.id', $order);


        if (!empty($titleEn)) {
            $batchBuilder->where('batches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $batchBuilder->where('batches.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $courseConfigBuilder */
        if ($paginate) {
            $batches = $batchBuilder->paginate(10);
            $paginateData = (object)$batches->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $batches = $batchBuilder->get();
        }

        $data = [];
        foreach ($batches as $batch) {
            /** @var Batche $batch */
            $links['read'] = route('api.v1.batches.read', ['id' => $batch->id]);
            $links['update'] = route('api.v1.batches.update', ['id' => $batch->id]);
            $links['delete'] = route('api.v1.batches.destroy', ['id' => $batch->id]);
            $courseConfig['_links'] = $links;
            $data[] = $batch->toArray();
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
                    '_link' => route('api.v1.batches.get-list')

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
    public function getOneCourseConfig(int $id, Carbon $startTime): array
    {
        /** @var Batche|Builder $batchBuilder */

        $batchBuilder = Batche::select([
            'batches.id as id',
            'batches.course_id',
            'courses.title_en as course_title',
            'batches.institute_id',
            'institutes.title_en as institute_title',
            'institutes.id as institute_id',
            'branches.id as branch_id',
            'branches.title_en as branch_name',
            'batches.programme_id',
            'programmes.title_en as programme_name',
            'training_centers.id as training_center_id',
            'training_centers.title_en as training_center_name',
            'batches.in_ethnic_group',
            'batches.is_freedom_fighter',
            'batches.disability_status',
            'batches.ssc_passing_status',
            'batches.hsc_passing_status',
            'batches.honors_passing_status',
            'batches.masters_passing_status',
            'batches.is_occupation_needed',
            'batches.is_guardian_info_needed',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at'
        ]);

        $batchBuilder->join('courses', 'batches.course_id', '=', 'courses.id');
        $batchBuilder->join('institutes', 'batches.institute_id', '=', 'institutes.id');
        $batchBuilder->leftJoin('programmes', 'batches.programme_id', '=', 'programmes.id');
        $batchBuilder->leftJoin('branches', 'batches.branch_id', '=', 'branches.id');
        $batchBuilder->leftJoin('training_centers', 'batches.training_center_id', '=', 'training_centers.id');
        $batchBuilder->where('batches.id', $id);

        /** @var Batche $instituteBuilder */
        $batch = $batchBuilder->first();

        $links = [];
        if ($batch) {
            $links['update'] = route('api.v1.batches.update', ['id' => $id]);
            $links['delete'] = route('api.v1.batches.destroy', ['id' => $id]);
        }
        return [
            "data" => $batch ?: null,
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
     * @return Batche
     */
    public function store(array $data): Batche
    {
        $courseConfig = new Batche();
        $courseConfig->fill($data);
        $courseConfig->save();


        return $courseConfig;
    }

    /**
     * @param Batche $courseConfig
     * @param array $data
     * @return Batche
     */
    public function update(Batche $courseConfig, array $data): Batche
    {
        $courseConfig->fill($data);
        $courseConfig->save();
        return $courseConfig;

    }

    /**
     * @param Batche $courseConfig
     * @return Batche
     */
    public function destroy(Batche $courseConfig): Batche
    {
        if ($courseConfig->delete()) {
            foreach ($courseConfig->courseSessions() as $courseSession) {
                $courseSession->delete();
            }
        }

        return $courseConfig;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = Null): \Illuminate\Contracts\Validation\Validator
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
                'required',
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
            'number_of_vacancies' => [
                'int',
                'nullable'
            ],
            'registration_start_date' => [
                'date_format:d/m/Y',
                'nullable'
            ],
            'registration_end_date' => [
                'date_format:d/m/Y',
                'nullable'
            ],
            'batch_start_date' => [
                'date_format:d/m/Y',
                'nullable'
            ],

            'batch_end_date' => [
                'date_format:d/m/Y',
                'nullable'
            ],
            'available_vacancies' => [
                'int',
                'nullable'
            ],

            'in_ethnic_group' => [
                'boolean',
                'nullable'
            ],
            'is_freedom_fighter' => [
                'nullable',
                'boolean'
            ],
            'disability_status' => [
                'nullable',
                'boolean'
            ],
            'ssc_passing_status' => [
                'nullable',
                'boolean'
            ],
            'hsc_passing_status' => [
                'nullable',
                'boolean',
            ],
            'honors_passing_status' => [
                'nullable',
                'boolean',
            ],
            'masters_passing_status' => [
                'nullable',
                'boolean',
            ],
            'is_occupation_needed' => [
                'nullable',
                'boolean',
            ],
            'is_guardian_info_needed' => [
                'nullable',
                'boolean',
            ],
            'row_status' => [
                'required_if:' . $id . ',==,null',
                Rule::in([Batche::ROW_STATUS_ACTIVE, Batche::ROW_STATUS_INACTIVE]),
            ],
        ];

        $messages = [
            'course_sessions.*.session_name_bn.regex' => "Session Name(Bangla) is required in Bangla",
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

}
