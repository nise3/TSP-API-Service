<?php


namespace App\Services;


use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use App\Models\Institute;
use App\Models\RegisteredTrainingOrganization;
use App\Services\CommonServices\SmsService;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RegisteredTrainingOrganizationService
{

    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getRtoList(array $request, Carbon $startTime ,bool $isPublicApi = false): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $countryId = $request['rto_country_id'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var RegisteredTrainingOrganization|Builder $rtoBuilder */
        $rtoBuilder = RegisteredTrainingOrganization::select([
            'registered_training_organizations.id',
            'registered_training_organizations.institute_id',
            'registered_training_organizations.code',
            'registered_training_organizations.title',
            'registered_training_organizations.title_en',
            'registered_training_organizations.loc_division_id',
            'registered_training_organizations.country_id',
            'countries.title as country_title',
            'countries.title_en as country_title_en',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'registered_training_organizations.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'registered_training_organizations.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'registered_training_organizations.address',
            'registered_training_organizations.address_en',
            'registered_training_organizations.location_latitude',
            'registered_training_organizations.location_longitude',
            'registered_training_organizations.google_map_src',
            'registered_training_organizations.logo',
            'registered_training_organizations.phone_code',
            'registered_training_organizations.primary_phone',
            'registered_training_organizations.phone_numbers',
            'registered_training_organizations.primary_mobile',
            'registered_training_organizations.mobile_numbers',
            'registered_training_organizations.email',
            'registered_training_organizations.name_of_the_office_head',
            'registered_training_organizations.name_of_the_office_head_en',
            'registered_training_organizations.name_of_the_office_head_designation',
            'registered_training_organizations.name_of_the_office_head_designation_en',
            'registered_training_organizations.contact_person_name',
            'registered_training_organizations.contact_person_name_en',
            'registered_training_organizations.contact_person_mobile',
            'registered_training_organizations.contact_person_email',
            'registered_training_organizations.contact_person_designation',
            'registered_training_organizations.contact_person_designation_en',
            'registered_training_organizations.config',
            'registered_training_organizations.row_status',
            'registered_training_organizations.created_by',
            'registered_training_organizations.updated_by',
            'registered_training_organizations.created_at',
            'registered_training_organizations.updated_at',
            'registered_training_organizations.deleted_at',
        ]);

        if (!$isPublicApi) {
            $rtoBuilder->acl();
        }

        $rtoBuilder->orderBy('registered_training_organizations.id', $order);

        $rtoBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'registered_training_organizations.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $rtoBuilder->leftJoin('loc_districts', function ($join) use ($rowStatus) {
            $join->on('loc_districts.id', '=', 'registered_training_organizations.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $rtoBuilder->leftJoin('loc_upazilas', function ($join) use ($rowStatus) {
            $join->on('loc_upazilas.id', '=', 'registered_training_organizations.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $rtoBuilder->leftJoin('countries', function ($join) use ($rowStatus) {
            $join->on('countries.id', '=', 'registered_training_organizations.country_id')
                ->whereNull('countries.deleted_at');
        });

        if (is_numeric($rowStatus)) {
            $rtoBuilder->where('registered_training_organizations.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $rtoBuilder->where('registered_training_organizations.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $rtoBuilder->where('registered_training_organizations.title', 'like', '%' . $title . '%');
        }

        if (!empty($instituteId)) {
            $rtoBuilder->where('registered_training_organizations.institute_id', $instituteId);
        }

        if (!empty($countryId)) {
            $rtoBuilder->where('registered_training_organizations.country_id', $countryId);
        }

        /** @var Collection $rtos */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $rtos = $rtoBuilder->paginate($pageSize);
            $paginateData = (object)$rtos->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rtos = $rtoBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $rtos->toArray()['data'] ?? $rtos->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RegisteredTrainingOrganization
     */
    public function getOneRto(int $id): RegisteredTrainingOrganization
    {
        /** @var RegisteredTrainingOrganization|Builder $registeredTrainingOrganizationBuilder */
        $registeredTrainingOrganizationBuilder = RegisteredTrainingOrganization::select([
            'registered_training_organizations.id',
            'registered_training_organizations.code',
            'registered_training_organizations.institute_id',
            'registered_training_organizations.title',
            'registered_training_organizations.title_en',
            'registered_training_organizations.loc_division_id',
            'registered_training_organizations.country_id',
            'countries.title as country_title',
            'countries.title_en as country_title_en',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
            'registered_training_organizations.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'registered_training_organizations.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
            'registered_training_organizations.address',
            'registered_training_organizations.address_en',
            'registered_training_organizations.location_latitude',
            'registered_training_organizations.location_longitude',
            'registered_training_organizations.google_map_src',
            'registered_training_organizations.logo',
            'registered_training_organizations.phone_code',
            'registered_training_organizations.primary_phone',
            'registered_training_organizations.phone_numbers',
            'registered_training_organizations.primary_mobile',
            'registered_training_organizations.mobile_numbers',
            'registered_training_organizations.email',
            'registered_training_organizations.name_of_the_office_head',
            'registered_training_organizations.name_of_the_office_head_en',
            'registered_training_organizations.name_of_the_office_head_designation',
            'registered_training_organizations.name_of_the_office_head_designation_en',
            'registered_training_organizations.contact_person_name',
            'registered_training_organizations.contact_person_name_en',
            'registered_training_organizations.contact_person_mobile',
            'registered_training_organizations.contact_person_email',
            'registered_training_organizations.contact_person_designation',
            'registered_training_organizations.contact_person_designation_en',
            'registered_training_organizations.config',
            'registered_training_organizations.row_status',
            'registered_training_organizations.created_by',
            'registered_training_organizations.updated_by',
            'registered_training_organizations.created_at',
            'registered_training_organizations.updated_at',
            'registered_training_organizations.deleted_at',
        ]);

        $registeredTrainingOrganizationBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'registered_training_organizations.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $registeredTrainingOrganizationBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_districts.id', '=', 'registered_training_organizations.loc_district_id')
                ->whereNull('loc_districts.deleted_at');
        });

        $registeredTrainingOrganizationBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_upazilas.id', '=', 'registered_training_organizations.loc_upazila_id')
                ->whereNull('loc_upazilas.deleted_at');
        });

        $registeredTrainingOrganizationBuilder->leftJoin('countries', function ($join){
            $join->on('countries.id', '=', 'registered_training_organizations.country_id')
                ->whereNull('countries.deleted_at');
        });

        $registeredTrainingOrganizationBuilder->where('registered_training_organizations.id', $id);

        return $registeredTrainingOrganizationBuilder->firstOrFail();
    }

    /**
     * @param RegisteredTrainingOrganization $rto
     * @param array $data
     * @return RegisteredTrainingOrganization
     */
    public function store(RegisteredTrainingOrganization $rto, array $data): RegisteredTrainingOrganization
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }


        $rto->fill($data);
        $rto->save();

        if (!empty($data['rto_sector_exceptions'])) {
            $rto->sectorExceptions()->sync($data['rto_sector_exceptions']);
        }
        if (!empty($data['rto_occupation_exceptions'])) {
            $rto->occupationExceptions()->sync($data['rto_occupation_exceptions']);
        }

        return $rto;
    }

    /**
     * @param RegisteredTrainingOrganization $rto
     * @param array $data
     * @return RegisteredTrainingOrganization
     */
    public function update(RegisteredTrainingOrganization $rto, array $data): RegisteredTrainingOrganization
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }

        $rto->fill($data);
        $rto->save();


        if (!empty($data['rto_sector_exceptions'])) {
            $rto->sectorExceptions()->sync($data['rto_sector_exceptions']);
        }
        if (!empty($data['rto_occupation_exceptions'])) {
            $rto->occupationExceptions()->sync($data['rto_occupation_exceptions']);
        }


        return $rto;
    }

    /**
     * @param RegisteredTrainingOrganization $rto
     * @return bool
     */
    public function destroy(RegisteredTrainingOrganization $rto): bool
    {
        return $rto->delete();
    }

    public function parseGoogleMapSrc(?string $googleMapSrc): ?string
    {
        if (!empty($googleMapSrc) && preg_match('/src="([^"]+)"/', $googleMapSrc, $match)) {
            $googleMapSrc = $match[1];
        }
        return $googleMapSrc;
    }


    /**
     * @param RegisteredTrainingOrganization $rto
     * @return mixed
     * @throws RequestException
     */
    public function rtoUserDestroy(RegisteredTrainingOrganization $rto): mixed
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'user-delete';
        $userPostField = [
            'user_type' => BaseModel::REGISTERED_TRAINING_ORGANIZATION_USER_TYPE,
            'registered_training_organization_id' => $rto->id,
        ];

        return Http::withOptions(
            [
                'verify' => config('nise3.should_ssl_verify'),
                'debug' => config('nise3.http_debug')
            ])
            ->timeout(5)
            ->delete($url, $userPostField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }

    /**
     * @param array $data
     * @return PromiseInterface|\Illuminate\Http\Client\Response|array
     * @throws RequestException
     */
    public function createUser(array $data): PromiseInterface|\Illuminate\Http\Client\Response|array
    {
        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'admin-user-create';
        $userPostField = [
            'permission_sub_group_id' => $data['permission_sub_group_id'],
            'user_type' => BaseModel::REGISTERED_TRAINING_ORGANIZATION_USER_TYPE,
            'registered_training_organization_id' => $data['registered_training_organization_id'],
            'username' => $data['contact_person_mobile'],
            'name_en' => $data['contact_person_name'],
            'name' => $data['contact_person_name'],
            'email' => $data['contact_person_email'],
            'mobile' => $data['contact_person_mobile'],
        ];

        return Http::withOptions([
            'verify' => config("nise3.should_ssl_verify"),
            'debug' => config('nise3.http_debug')
        ])
            ->timeout(5)
            ->post($url, $userPostField)
            ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($url) {
                Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ' . $httpResponse->body());
                throw new HttpErrorException($httpResponse);
            })
            ->json();
    }

    public function userInfoSendBySMS(string $recipient, string $message)
    {
        $sms = new SmsService();
        $sms->sendSms($recipient, $message);
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
            $data["phone_numbers"] = isset($data['phone_numbers']) && is_array($data['phone_numbers']) ? $data['phone_numbers'] : explode(',', $data['phone_numbers']);
        }
        if (!empty($data['mobile_numbers'])) {
            $data["mobile_numbers"] = isset($data['mobile_numbers']) && is_array($data['mobile_numbers']) ? $data['mobile_numbers'] : explode(',', $data['mobile_numbers']);
        }
        if (!empty($data['rto_occupation_exceptions'])) {
            $data["rto_occupation_exceptions"] = isset($data['rto_occupation_exceptions']) && is_array($data['rto_occupation_exceptions']) ? $data['rto_occupation_exceptions'] : explode(',', $data['rto_occupation_exceptions']);
        }
        if (!empty($data['rto_sector_exceptions'])) {
            $data["rto_sector_exceptions"] = isset($data['rto_sector_exceptions']) && is_array($data['rto_sector_exceptions']) ? $data['rto_sector_exceptions'] : explode(',', $data['rto_sector_exceptions']);
        }

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $rules = [
            'rto_sector_exceptions' => [
                'nullable',
                'array',
            ],
            'rto_sector_exceptions.*' => [
                Rule::requiredIf(!empty($data['rto_sector_exceptions'])),
                'nullable',
                'int',
                'distinct',
                'exists:rpl_sectors,id,deleted_at,NULL',
            ],
            'rto_occupation_exceptions' => [
                'array',
                'nullable',
            ],
            'rto_occupation_exceptions.*' => [
                Rule::requiredIf(!empty($data['rto_occupation_exceptions'])),
                'nullable',
                'int',
                'distinct',
                'exists:rpl_occupations,id,deleted_at,NULL',
            ],
            'permission_sub_group_id' => [
                Rule::requiredIf(function () use ($id) {
                    return is_null($id);
                }),
                'nullable',
                'int'
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id,deleted_at,NULL'
            ],
            "code" => [
                'string',
                'required'
            ],
            'title' => [
                'required',
                'string',
                'max:1000',
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'country_id' => [
                'required',
                'integer',
                'exists:rto_countries,country_id'
            ],
            'loc_division_id' => [
                'required',
                'integer',
                'exists:loc_divisions,id,deleted_at,NULL'
            ],
            'loc_district_id' => [
                'required',
                'integer',
                'exists:loc_districts,id,deleted_at,NULL'
            ],
            'loc_upazila_id' => [
                'nullable',
                'integer',
                'exists:loc_upazilas,id,deleted_at,NULL'
            ],
            'location_latitude' => [
                'nullable',
                'string',
                'max:50'
            ],
            'location_longitude' => [
                'nullable',
                'string',
                'max:50'
            ],
            'google_map_src' => [
                'nullable',
                'string'
            ],
            'address' => [
                'nullable',
                'string'
            ],
            'address_en' => [
                'nullable',
                'string'
            ],
            'logo' => [
                'nullable',
                'string',
            ],
            'primary_phone' => [
                'nullable',
                'string',
                'max:20'
            ],
            'phone_numbers' => [
                'nullable',
                'array'
            ],
            'phone_numbers.*' => [
                'nullable',
                'string'
            ],
            'primary_mobile' => [
                'required',
                'string',
                BaseModel::MOBILE_REGEX
            ],
            'mobile_numbers' => [
                'nullable',
                'array'
            ],
            'mobile_numbers.*' => [
                'nullable',
                'string',
                BaseModel::MOBILE_REGEX
            ],

            'email' => [
                'required',
                'email',
                'max:254'
            ],

            'name_of_the_office_head' => [
                'required',
                'string',
                'max:500'
            ],
            'name_of_the_office_head_en' => [
                'nullable',
                'string'
            ],
            'name_of_the_office_head_designation' => [
                "required",
                "string",
                "max:500"
            ],
            'name_of_the_office_head_designation_en' => [
                "nullable",
                "string",
                "max:500"
            ],
            'contact_person_name' => [
                'required',
                'max: 500',
                'min:2'
            ],
            'contact_person_name_en' => [
                'nullable',
                'max: 250',
                'min:2'
            ],
            'contact_person_mobile' => [
                'required',
                BaseModel::MOBILE_REGEX,
                Rule::unique('registered_training_organizations', 'contact_person_mobile')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'contact_person_email' => [
                'required',
                'email',
                Rule::unique('registered_training_organizations', 'contact_person_email')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'contact_person_designation' => [
                'required',
                'max: 500',
                "min:2"
            ],
            'contact_person_designation_en' => [
                'nullable',
                'max: 300',
                "min:2"
            ],
            'config' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                'nullable',
                Rule::in(Institute::ROW_STATUSES),
            ],
            'created_by' => ['nullable', 'int'],
            'updated_by' => ['nullable', 'int'],

        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
    }


    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'institute_id' => 'nullable|min:1',
            'rto_country_id' => 'nullable|min:1',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "nullable",
                "int",
                Rule::in(Institute::ROW_STATUSES),
            ],
        ], $customMessage);
    }
}
