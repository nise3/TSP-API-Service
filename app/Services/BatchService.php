<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\TrainingCenter;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getBatchList(array $request, Carbon $startTime): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $instituteId = $request['institute_id'] ?? "";
        $industryAssociationId = $request['industry_association_id'] ?? "";
        $branchId = $request['branch_id'] ?? "";
        $programId = $request['program_id'] ?? "";
        $courseId = $request['course_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";


        /** @var Batch|Builder $batchBuilder */
        $batchBuilder = Batch::select([
            'batches.id',
            'batches.title',
            'batches.title_en',
            'batches.course_id',
            'courses.title_en as course_title_en',
            'courses.title as course_title',
            'batches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'batches.industry_association_id',
            'batches.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'courses.program_id',
            'programs.title_en as program_title_en',
            'programs.title as program_title',
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
            'batches.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            'courses.application_form_settings',
            'batches.row_status',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at',
            'batches.deleted_at',
        ])->acl();

        $batchBuilder->leftJoin("courses", function ($join) use ($rowStatus) {
            $join->on('batches.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
        });
        $batchBuilder->leftjoin("institutes", function ($join) use ($rowStatus) {
            $join->on('batches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $batchBuilder->leftjoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
        });

        $batchBuilder->leftjoin("branches", function ($join) use ($rowStatus) {
            $join->on('batches.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $batchBuilder->join("training_centers", function ($join) use ($rowStatus) {
            $join->on('batches.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $batchBuilder->orderBy('batches.id', $order);

        if (is_numeric($rowStatus)) {
            $batchBuilder->where('batches.row_status', $rowStatus);
        }

        if (is_numeric($instituteId)) {
            $batchBuilder->where('batches.institute_id', $instituteId);
        }
        if (is_numeric($industryAssociationId)) {
            $batchBuilder->where('batches.industry_association_id', $industryAssociationId);
        }
        if (is_numeric($branchId)) {
            $batchBuilder->where('batches.branch_id', $branchId);
        }

        if (is_numeric($programId)) {
            $batchBuilder->where('courses.program_id', $programId);
        }

        if (is_numeric($courseId)) {
            $batchBuilder->where('batches.course_id', $courseId);
        }

        if (is_numeric($trainingCenterId)) {
            $batchBuilder->where('batches.training_center_id', $trainingCenterId);
        }

        /** @var Collection $batches */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $batches = $batchBuilder->paginate($pageSize);
            $paginateData = (object)$batches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $batches = $batchBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $batches->toArray()['data'] ?? $batches->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return Batch
     */
    public function getBatch(int $id): Batch
    {
        /** @var Batch|Builder $batchBuilder */

        $batchBuilder = Batch::select([
            'batches.id',
            'batches.title',
            'batches.title_en',
            'batches.course_id',
            'courses.title_en as course_title_en',
            'courses.title as course_title',
            'batches.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'batches.industry_association_id',
            'batches.branch_id',
            'branches.title_en as branch_title_en',
            'branches.title as branch_title',
            'courses.program_id',
            'programs.title_en as program_title_en',
            'programs.title as program_title',
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
            'batches.training_center_id',
            'training_centers.title_en as training_center_title_en',
            'training_centers.title as training_center_title',
            'courses.application_form_settings',
            'batches.row_status',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at',
            'batches.deleted_at',
        ]);

        $batchBuilder->leftJoin("courses", function ($join) {
            $join->on('batches.course_id', '=', 'courses.id')
                ->whereNull('courses.deleted_at');
        });
        $batchBuilder->leftjoin("institutes", function ($join) {
            $join->on('batches.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $batchBuilder->leftjoin("programs", function ($join) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
        });

        $batchBuilder->leftjoin("branches", function ($join) {
            $join->on('batches.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });
        $batchBuilder->join("training_centers", function ($join) {
            $join->on('batches.training_center_id', '=', 'training_centers.id')
                ->whereNull('training_centers.deleted_at');
        });

        $batchBuilder->where('batches.id', $id);

        $batchBuilder->with('trainers');

        /** @var Batch $batch */
        return $batchBuilder->firstOrFail();
    }


    /**
     * @param array $data
     * @return Batch
     */
    public function store(array $data): Batch
    {
        $courseConfig = new Batch();
        $courseConfig->fill($data);
        $courseConfig['available_seats'] = $data['number_of_seats'];
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
     * @param int $batchId
     * @return mixed
     * @throws RequestException
     */
    public function destroyCalenderEventByBatchId(int $batchId): mixed
    {
        $url = clientUrl(BaseModel::CMS_CLIENT_URL_TYPE) . 'delete-calender-event-by-batch-id/' . $batchId;
        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->delete($url)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                return $e;
            })
            ->json();
    }


    public function getBatchTrashList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $title = $request->query('title');
        $paginate = $request->query('page');
        $limit = $request->query('limit', 10);
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Batch|Builder $batchBuilder */
        $batchBuilder = Batch::onlyTrashed()->select([
            'batches.id as id',
            'batches.course_id',
            'courses.title_en as course_title',
            'batches.institute_id',
            'institutes.title_en as institute_title',
            'institutes.id as institute_id',
            'branches.id as branch_id',
            'branches.title_en as branch_name',
            'courses.program_id',
            'programs.title_en as program_name',
            'programs.title as program_name_bn',
            'batches.number_of_seats',
            'batches.registration_start_date',
            'batches.registration_end_date',
            'batches.batch_start_date',
            'batches.batch_end_date',
            'batches.available_seats',
            'training_centers.id as training_center_id',
            'training_centers.title_en as training_center_name',
            'batches.row_status',
            'batches.created_by',
            'batches.updated_by',
            'batches.created_at',
            'batches.updated_at'
        ]);

        $batchBuilder->join('courses', 'batches.course_id', '=', 'courses.id');
        $batchBuilder->join('institutes', 'batches.institute_id', '=', 'institutes.id');
        $batchBuilder->leftJoin('programs', 'courses.program_id', '=', 'programs.id');
        $batchBuilder->leftJoin('branches', 'batches.branch_id', '=', 'branches.id');
        $batchBuilder->leftJoin('training_centers', 'batches.training_center_id', '=', 'training_centers.id');

        $batchBuilder->orderBy('batches.id', $order);


        if (!empty($titleEn)) {
            $batchBuilder->where('batches.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($title)) {
            $batchBuilder->where('batches.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $batches */
        if (is_numeric($paginate) || is_numeric($limit)) {
            $limit = $limit ?: 10;
            $batches = $batchBuilder->paginate($limit);
            $paginateData = (object)$batches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $batches = $batchBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $batches->toArray()['data'] ?? $batches->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param Batch $batch
     * @param array $trainerIds
     * @return Batch
     */
    public function assignTrainer(Batch $batch, array $trainerIds): Batch
    {
        $validTrainers = Trainer::whereIn('id', $trainerIds)->orderBy('id', 'ASC')->pluck('id')->toArray();
        $batch->trainers()->sync($validTrainers);
        return $batch;
    }

    public function restore(Batch $batch): bool
    {
        return $batch->restore();
    }

    public function forceDelete(Batch $batch): bool
    {
        return $batch->forceDelete();
    }


    /**
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, int $id = Null): \Illuminate\Contracts\Validation\Validator
    {

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];
        $rules = [
            'title' => [
                'required',
                'string',
                'max:500'
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            'branch_id' => [
                'exists:branches,id,deleted_at,NULL',
                'nullable',
                'int',
            ],
            'training_center_id' => [
                'exists:training_centers,id,deleted_at,NULL',
                'required',
                'int',
            ],
            'course_id' => [
                'exists:courses,id,deleted_at,NULL',
                'required',
                'int',
            ],
            'number_of_seats' => [
                'required',
                'int'
            ],
            'registration_start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before:registration_end_date'
            ],
            'registration_end_date' => [
                'required',
                'date',
                'after:registration_start_date',
                'date_format:Y-m-d',
            ],
            'batch_start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before:batch_end_date'
            ],
            'batch_end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:batch_start_date'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => [
                'nullable',
                'integer',
                'max:10'
            ],
            'updated_by' => [
                'nullable',
                'integer',
                'max:10'
            ],
        ];

        $rules = array_merge(BaseModel::industryOrIndustryAssociationValidationRules(), $rules);

        return Validator::make($request->all(), $rules, $customMessage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $rules = [
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'course_id' => 'nullable|int|exists:courses,id,deleted_at,NULL',
            'branch_id' => 'nullable|int|exists:branches,id,deleted_at,NULL',
            'program_id' => 'nullable|int|exists:programs,id,deleted_at,NULL',
            'training_center_id' => 'nullable|int|exists:training_centers,id,deleted_at,NULL',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];

        $rules = array_merge(BaseModel::industryOrIndustryAssociationValidationRulesForFilter(), $rules);
        return Validator::make($request->all(), $rules, $customMessage);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function trainerValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();

        $data = [];
        if (!empty($requestData['trainerIds'])) {
            $data["trainerIds"] = is_array($requestData['trainerIds']) ? $requestData['trainerIds'] : explode(',', $requestData['trainerIds']);
        }

        $rules = [
            'trainerIds' => 'required|array',
            'trainerIds.*' => 'nullable|integer|distinct|exists:trainers,id,deleted_at,NULL'
        ];
        return Validator::make($data, $rules);
    }


    /**
     * @param Request $request
     * @param $id
     * @param $currentTime
     * @return array
     */
    public function batchesWithTrainingCenters(Request $request, $id, $currentTime): array
    {
        $active = $request->get('active') === "true";
        $upcoming = $request->get('upcoming') === "true";

        /** @var Course $course */
        $course = Course::findOrFail($id);

        $queryAttribute = "institute_id";
        $queryAttributeValue = $course->institute_id;
        if (Auth::user()->isIndustryAssociationUser()) {
            $queryAttribute = "industry_association_id";
            $queryAttributeValue = $course->industry_association_id;
        }

        /** @var Course|Builder $courseBuilder */
        $courseBuilder = TrainingCenter::select([
            'training_centers.id',
            'training_centers.title',
            'training_centers.title_en',

            'loc_divisions.title_en as division_title_en',
            'loc_divisions.title as division_title',
            'loc_divisions.bbs_code as division_bbs_code',

            'loc_districts.title_en as district_title_en',
            'loc_districts.title as district_title',
            'loc_districts.bbs_code as district_bbs_code',
            'loc_districts.is_sadar_district',

            'loc_upazilas.title_en as upazila_title_en',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.bbs_code as upazila_bbs_code',
            'loc_upazilas.is_sadar_upazila',

            'training_centers.address',
            'training_centers.address_en',
            'training_centers.location_latitude',
            'training_centers.location_longitude',
            'training_centers.google_map_src',
            'training_centers.row_status',

            DB::raw('GROUP_CONCAT(batches.id) as batch_ids'),
            DB::raw('GROUP_CONCAT(batches.title) as batch_titles'),
            DB::raw('GROUP_CONCAT(batches.title_en) as batch_titles_en'),
            DB::raw('GROUP_CONCAT(batches.number_of_seats) as number_of_seats'),
            DB::raw('GROUP_CONCAT(batches.registration_start_date) as registration_start_dates'),
            DB::raw('GROUP_CONCAT(batches.registration_end_date) as registration_end_dates'),
            DB::raw('GROUP_CONCAT(batches.batch_start_date) as batch_start_dates'),
            DB::raw('GROUP_CONCAT(batches.batch_end_date) as batch_end_dates'),
            DB::raw('GROUP_CONCAT(batches.available_seats) as available_seats'),
            DB::raw('GROUP_CONCAT(batches.row_status) as batch_row_statuses')
        ]);

        $courseBuilder->join('batches', function ($join) use ($currentTime, $active, $upcoming) {
            $join->on('batches.training_center_id', '=', 'training_centers.id');
            if ($active && !$upcoming) {
                $join->whereDate('batches.registration_start_date', '<=', $currentTime);
                $join->whereDate('batches.registration_end_date', '>=', $currentTime);
            } else if (!$active && $upcoming) {
                $join->whereDate('batches.registration_start_date', '>', $currentTime);
            } else {
                $join->whereDate('batches.registration_end_date', '>=', $currentTime);
            }
        })
            ->leftJoin('loc_divisions', 'loc_divisions.id', '=', 'training_centers.loc_division_id')
            ->leftJoin('loc_districts', 'loc_districts.id', '=', 'training_centers.loc_district_id')
            ->leftJoin('loc_upazilas', 'loc_upazilas.id', '=', 'training_centers.loc_upazila_id')
            ->where([
                ['training_centers.' . $queryAttribute, '=', $queryAttributeValue],
                ['batches.course_id', '=', $id],
                ['batches.' . $queryAttribute, '=', $queryAttributeValue]
            ])
            ->groupBy('training_centers.id')
            ->whereNull('training_centers.deleted_at')
            ->whereNull('batches.deleted_at');

        $result = $courseBuilder->get();

        $trainingCenterWiseBatches = $result->toArray()['data'] ?? $result->toArray();

        /** Generate batches from GROUP_CONCAT results */
        if (count($trainingCenterWiseBatches) > 0) {
            $length = count($trainingCenterWiseBatches);
            for ($index = 0; $index < $length; ++$index) {

                $batchIds = explode(',', $trainingCenterWiseBatches[$index]['batch_ids']);
                $batchTitles = explode(',', $trainingCenterWiseBatches[$index]['batch_titles']);
                $batchTitlesEn = explode(',', $trainingCenterWiseBatches[$index]['batch_titles_en']);
                $numberOfSeats = explode(',', $trainingCenterWiseBatches[$index]['number_of_seats']);
                $registrationStartDates = explode(',', $trainingCenterWiseBatches[$index]['registration_start_dates']);
                $registrationEndDate = explode(',', $trainingCenterWiseBatches[$index]['registration_end_dates']);
                $batchStartDate = explode(',', $trainingCenterWiseBatches[$index]['batch_start_dates']);
                $batchEndDate = explode(',', $trainingCenterWiseBatches[$index]['batch_end_dates']);
                $availableSeats = explode(',', $trainingCenterWiseBatches[$index]['available_seats']);
                $batchRowStatus = explode(',', $trainingCenterWiseBatches[$index]['batch_row_statuses']);

                $trainingCenterWiseBatches[$index]['batches'] = [];

                /** Generate all batches under a Training_Center with required information */
                $batchCount = count($batchIds);
                for ($i = 0; $i < $batchCount; ++$i) {
                    $batchInfo = [
                        "id" => $batchIds[$i],
                        "title" => $batchTitles[$i],
                        "title_en" => $batchTitlesEn[$i],
                        "number_of_seat" => $numberOfSeats[$i],
                        "registration_start_date" => $registrationStartDates[$i],
                        "registration_end_date" => $registrationEndDate[$i],
                        "batch_start_date" => $batchStartDate[$i],
                        "batch_end_date" => $batchEndDate[$i],
                        "available_seats" => $availableSeats[$i],
                        "batch_row_status" => $batchRowStatus[$i]
                    ];
                    $trainingCenterWiseBatches[$index]['batches'][$i] = $batchInfo;
                }

                /** Delete all unused elements from Final Array */
                unset($trainingCenterWiseBatches[$index]['batch_ids']);
                unset($trainingCenterWiseBatches[$index]['number_of_seats']);
                unset($trainingCenterWiseBatches[$index]['registration_start_dates']);
                unset($trainingCenterWiseBatches[$index]['registration_end_dates']);
                unset($trainingCenterWiseBatches[$index]['batch_start_dates']);
                unset($trainingCenterWiseBatches[$index]['batch_end_dates']);
                unset($trainingCenterWiseBatches[$index]['available_seats']);
                unset($trainingCenterWiseBatches[$index]['batch_row_statuses']);
            }
        }

        $response = [];
        $response['data'] = $trainingCenterWiseBatches;

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $currentTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param array $batch
     * @return array
     * @throws RequestException
     */
    public function createCalenderEventForBatch(array $batch): array
    {
        $url = clientUrl(BaseModel::CMS_CLIENT_URL_TYPE) . 'calendar-update-after-batch-create';

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->post($url, $batch)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                return $e;
            })
            ->json();
    }

    /**
     * Update calenderEvent on batch update
     * @throws RequestException
     */
    public function updateCalenderEventOnBatchUpdate(array $data)
    {
        $batchId = $data['id'];
        $url = clientUrl(BaseModel::CMS_CLIENT_URL_TYPE) . 'update-calender-event-after-batch-update/' . $batchId;
        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug'),
            'timeout' => config("nise3.http_timeout")
        ])
            ->put($url, $data)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . json_encode($response));
                return $e;
            })
            ->json();
    }


}

