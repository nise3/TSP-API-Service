<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\Batch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BatchService
 * @package App\Services
 */
class BatchService
{

    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getBatchList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $limit = $request->query('limit', 10);
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Batch|Builder $batchBuilder */
        $batchBuilder = Batch::select([
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
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
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
            $batches = $batchBuilder->paginate($limit);
            $paginateData = (object)$batches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $batches = $batchBuilder->get();
        }

        $response['order']=$order;
        $response['data']=$batches->toArray()['data'] ?? $batches->toArray();

        $response['response_status']= [
            "success" => true,
            "code" => Response::HTTP_OK,
            "started" => $startTime->format('H i s'),
            "finished" => Carbon::now()->format('H i s'),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getBatch(int $id, Carbon $startTime): array
    {
        /** @var Batch|Builder $batchBuilder */

        $batchBuilder = Batch::select([
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
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
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

        /** @var Batch $instituteBuilder */
        $batch = $batchBuilder->first();

        return [
            "data" => $batch ?: [],
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
     * @return Batch
     */
    public function store(array $data): Batch
    {
        $courseConfig = new Batch();
        $courseConfig->fill($data);
        $courseConfig->save();
        return $courseConfig;
    }

    /**
     * @param Batch $courseConfig
     * @param array $data
     * @return Batch
     */
    public function update(Batch $courseConfig, array $data): Batch
    {
        $courseConfig->fill($data);
        $courseConfig->save();
        return $courseConfig;

    }

    /**
     * @param Batch $batch
     * @return bool
     */
    public function destroy(Batch $batch): bool
    {
        return $batch->delete();
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = Null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'institute_id' => [
                'nullable',
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
                'int',
                'exists:training_centers,id'
            ],
            'programme_id' => [
                'nullable',
                'int',
                'exists:programmes,id'
            ],
            'branch_id' => [
                'nullable',
                'int',
                'exists:branches,id'
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
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }

}
