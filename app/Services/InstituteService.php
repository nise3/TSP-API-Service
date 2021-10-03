<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Institute;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title_bn'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Institute|Builder $instituteBuilder */
        $instituteBuilder = Institute::select([
            'institutes.id',
            "institutes.institute_type_id",
            'institutes.code',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.domain',
            'institutes.address',
            'institutes.google_map_src',
            'institutes.logo',
            'institutes.country',
            'institutes.phone_code',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.name_of_the_office_head',
            'institutes.name_of_the_office_head_designation',
            'institutes.contact_person_name',
            'institutes.contact_person_mobile',
            'institutes.contact_person_email',
            'institutes.contact_person_designation',
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
            'institutes.row_status',
            'institutes.created_by',
            'institutes.updated_by',
            'institutes.created_at',
            'institutes.updated_at',
            'institutes.deleted_at',
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
        }
        if (!empty($titleBn)) {
            $instituteBuilder->where('institutes.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $institutes */
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
            'institutes.id',
            "institutes.institute_type_id",
            'institutes.code',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.domain',
            'institutes.address',
            'institutes.google_map_src',
            'institutes.logo',
            'institutes.country',
            'institutes.phone_code',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.name_of_the_office_head',
            'institutes.name_of_the_office_head_designation',
            'institutes.contact_person_name',
            'institutes.contact_person_mobile',
            'institutes.contact_person_email',
            'institutes.contact_person_designation',
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
            'institutes.row_status',
            'institutes.created_by',
            'institutes.updated_by',
            'institutes.created_at',
            'institutes.updated_at',
            'institutes.deleted_at',
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

        /** @var Institute $institute */
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


    public function parseGoogleMapSrc(?string $googleMapSrc): ?string
    {
        if (!empty($googleMapSrc) && preg_match('/src="([^"]+)"/', $googleMapSrc, $match)) {
            $googleMapSrc = $match[1];
        }
        return $googleMapSrc;
    }

    /**
     * @param Institute $institute
     * @param array $data
     * @return Institute
     */
    public function store(Institute $institute, array $data): Institute
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


    /**
     * @param array $data
     * @return PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RequestException
     */
    public function createUser(array $data)
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'organization-or-institute-user-create';
        $userPostField = [
            'permission_sub_group_id' => $data['permission_sub_group_id'],
            'user_type' => BaseModel::INSTITUTE_USER,
            'institute_id' => $data['institute_id'],
            'username' => $data['contact_person_mobile'],
            'name_en' => $data['contact_person_name'],
            'name_bn' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
        ];

        Log::info(json_encode($userPostField));

        return Http::retry(3)->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json();
    }

    public function createRegisterUser(array $data)
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'register-user';

        $userPostField = [
            'user_type' => BaseModel::INSTITUTE_USER,
            'username' => $data['contact_person_mobile'],
            'institute_id' => $data['institute_id'],
            'name_en' => $data['contact_person_name'],
            'name_bn' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
            'password' => $data['password']
        ];

        return Http::retry(3)->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json();
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

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        if (!empty($data['phone_numbers'])) {
            $data["phone_numbers"] = is_array($request['phone_numbers']) ? $request['phone_numbers'] : explode(',', $request['phone_numbers']);
        }
        if (!empty($data['mobile_numbers'])) {
            $data["mobile_numbers"] = is_array($request['mobile_numbers']) ? $request['mobile_numbers'] : explode(',', $request['mobile_numbers']);
        }

        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        $rules = [
            'permission_sub_group_id' => 'required|numeric',
            'title_en' => ['required', 'string', 'max:400'],
            'title_bn' => ['required', 'string', 'max:1000'],
            "institute_type_id" => [
                "required",
                "numeric"
            ],
            'code' => ['required', 'string', 'max:191', 'unique:institutes,code,' . $id],
            'domain' => [
                'nullable',
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
            'primary_mobile' => ['required', 'string', BaseModel::MOBILE_REGEX],
            'mobile_numbers' => ['array'],
            'mobile_numbers.*' => ['nullable', 'string', BaseModel::MOBILE_REGEX],
            'logo' => [
                'nullable',
                'string',
            ],
            "name_of_the_office_head" => [
                "required",
                "string"
            ],
            "name_of_the_office_head_designation" => [
                "nullable",
                "string"
            ],
            'contact_person_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_designation' => [
                'required',
                'max: 300',
                "min:2"
            ],
            'contact_person_email' => [
                'required',
                'email'
            ],
            'email' => ['required', 'string', 'max:191'],
            'config' => ['nullable', 'string'],
            'loc_division_id' => ['nullable', 'integer'],
            'loc_district_id' => ['nullable', 'integer'],
            'loc_upazila_id' => ['nullable', 'integer'],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],

        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }

    public function registerOrganizationvalidator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:300',
                'min:2',
            ],
            'title_bn' => [
                'required',
                'string',
                'max:1000',
                'min:2'
            ],
            'institute_type_id' => [
                'required',
                'int'
            ],
            "name_of_the_office_head" => [
                "required",
                "string"
            ],
            "name_of_the_office_head_designation" => [
                "nullable",
                "string"
            ],
            'email' => [
                'required',
                'email',
            ],
            'mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            'contact_person_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_designation' => [
                'required',
                'max: 300',
                "min:2"
            ],
            'contact_person_email' => [
                'required',
                'email'
            ],
            'address' => [
                'required',
                'max: 1000',
                'min:2'
            ],
            "password" => [
                'required_with:password_confirmation',
                'string',
                'confirmed'
            ],
            "password_confirmation" => 'required_with:password',
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be either ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be either 1 or 0'
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'page_size' => 'numeric|gt:0',
            'page' => 'numeric|gt:0',
            "institute_type_id" => [
                "numeric"
            ],
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
