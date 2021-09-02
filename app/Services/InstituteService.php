<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Institute;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class InstituteService
 * @package App\Services
 */
class InstituteService
{
    public TrainingCenterService $trainingCenterService;

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getInstituteList(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $pageSize = array_key_exists('page_size', $request) ? $request['page_size'] : "";
        $paginate = array_key_exists('page', $request) ? $request['page'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var Institute|Builder $instituteBuilder */
        $instituteBuilder = Institute::select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.logo',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.config',
            'institutes.loc_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'institutes.loc_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'institutes.loc_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'institutes.domain',
            'institutes.address',
            'institutes.google_map_src',
            'institutes.row_status',
            'institutes.created_by',
            'institutes.updated_by',
            'institutes.created_at',
            'institutes.updated_at',
        ]);

        $instituteBuilder->orderBy('institutes.id', $order);

        $instituteBuilder->leftJoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('loc_divisions.id', '=', 'institutes.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_divisions.row_status', $rowStatus);
            }
        });

        $instituteBuilder->leftJoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('loc_districts.id', '=', 'institutes.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_districts.row_status', $rowStatus);
            }
        });

        $instituteBuilder->leftJoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('loc_upazilas.id', '=', 'institutes.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_upazilas.row_status', $rowStatus);
            }
        });

        if (is_numeric($rowStatus)) {
            $instituteBuilder->where('institutes.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $instituteBuilder->where('institutes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $instituteBuilder->where('institutes.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $instituteBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $institutes = $instituteBuilder->paginate($pageSize);
            $paginateData = (object)$institutes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $institutes = $instituteBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $institutes->toArray()['data'] ?? $institutes->toArray();

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
    public function getOneInstitute(int $id, Carbon $startTime): array
    {
        /** @var Institute|Builder $instituteBuilder */
        $instituteBuilder = Institute::select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.logo',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.config',
            'institutes.loc_division_id',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'institutes.loc_district_id',
            'loc_districts.title_bn as district_title_bn',
            'loc_districts.title_en as district_title_en',
            'institutes.loc_upazila_id',
            'loc_upazilas.title_bn as upazila_title_bn',
            'loc_upazilas.title_en as upazila_title_en',
            'institutes.domain',
            'institutes.address',
            'institutes.google_map_src',
            'institutes.row_status',
            'institutes.created_by',
            'institutes.updated_by',
            'institutes.created_at',
            'institutes.updated_at',
        ]);

        $instituteBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'institutes.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $instituteBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'institutes.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $instituteBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'institutes.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        if (!empty($id)) {
            $instituteBuilder->where('institutes.id', $id);
        }

        /** @var Institute $instituteBuilder */
        $institute = $instituteBuilder->first();

        return [
            "data" => $institute ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        $rules = [
            'title_en' => ['required', 'string', 'max:400'],
            'title_bn' => ['required', 'string', 'max:1000'],
            'code' => ['required', 'string', 'max:191', 'unique:institutes,code,' . $id],
            'domain' => [
                'required',
                'string',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'max:191',
                'unique:institutes,domain,' . $id
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'google_map_src' => ['nullable', 'string'],
            'primary_phone' => [
                'nullable',
                'regex:/^[0-9]*$/'
            ],
            'phone_numbers' => ['array'],
            'phone_numbers.*' => ['nullable', 'string', 'regex:/^[0-9]*$/'],
            'primary_mobile' => ['required', 'string', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'],
            'mobile_numbers' => ['array'],
            'mobile_numbers.*' => ['nullable', 'string', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'],
            'logo' => [
                'nullable',
                'string',
            ],
            'is_training_center' => 'nullable|boolean',
            'training_center_name_en' => 'nullable|string|max: 191',
            'training_center_name_bn' => 'nullable|string|max: 191',
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }

    public function parseGoogleMapSrc(?string $googleMapSrc): ?string
    {
        if (!empty($googleMapSrc) && preg_match('/src="([^"]+)"/', $googleMapSrc, $match)) {
            $googleMapSrc = $match[1];
        }
        return $googleMapSrc;
    }

    /**
     * @param array $data
     * @return Institute
     */
    public function store(array $data): Institute
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }

        $institute = new Institute();
        $institute->fill($data);
        $institute->save();
        return $institute;
    }

    /**
     * @param Institute $institute
     * @param array $data
     * @return Institute
     */
    public function update(Institute $institute, array $data): Institute
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $institute->fill($data);
        $institute->save();
        return $institute;
    }

    /**
     * @param Institute $institute
     * @return bool
     */
    public function destroy(Institute $institute): bool
    {
        return $institute->delete();
    }

    public function getInstituteTrashList(Request $request, Carbon $startTime): array
    {
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Institute|Builder $instituteBuilder */
        $instituteBuilder = Institute::onlyTrashed()->select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.logo',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.config',
            'institutes.domain',
            'institutes.address',
            'institutes.google_map_src',
            'institutes.row_status',
            'institutes.created_by',
            'institutes.updated_by',
            'institutes.created_at',
            'institutes.updated_at',
        ]);
        $instituteBuilder->orderBy('institutes.id', $order);

        if (!empty($titleEn)) {
            $instituteBuilder->where('institutes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $instituteBuilder->where('institutes.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $instituteBuilder */
        if (is_numeric($paginate) || is_numeric($limit)) {
            $limit = $limit ?: 10;
            $institutes = $instituteBuilder->paginate($limit);
            $paginateData = (object)$institutes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $institutes = $instituteBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $institutes->toArray()['data'] ?? $institutes->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    public function restore(Institute $institute): bool
    {
        return $institute->restore();
    }

    public function forceDelete(Institute $institute): bool
    {
        return $institute->forceDelete();
    }

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
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page_size' => 'numeric',
            'page' => 'numeric',
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
