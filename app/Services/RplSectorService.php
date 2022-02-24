<?php


namespace App\Services;


use App\Exceptions\HttpErrorException;
use App\Models\BaseModel;
use App\Models\Institute;
use App\Models\RegisteredTrainingOrganization;
use App\Models\RplSector;
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

class RplSectorService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getRplSectorList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var RplSector|Builder $rplSectorBuilder */
        $rplSectorBuilder = RplSector::select([
            'rpl_sectors.id',
            'rpl_sectors.title',
            'rpl_sectors.title_en',
            'rpl_sectors.translations',
            'rpl_sectors.created_at',
            'rpl_sectors.updated_at',
            'rpl_sectors.deleted_at',
        ]);

        $rplSectorBuilder->orderBy('rpl_sectors.id', $order);

        if (!empty($titleEn)) {
            $rplSectorBuilder->where('rpl_sectors.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $rplSectorBuilder->where('rpl_sectors.title', 'like', '%' . $title . '%');
        }

        /** @var Collection $rplSectors */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $rplSectors = $rplSectorBuilder->paginate($pageSize);
            $paginateData = (object)$rplSectors->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rplSectors = $rplSectorBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $rplSectors->toArray()['data'] ?? $rplSectors->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplSector
     */
    public function getOneRplSector(int $id): RplSector
    {
        /** @var RplSector|Builder $rplSectorBuilder */
        $rplSectorBuilder = RegisteredTrainingOrganization::select([
            'rpl_sectors.id',
            'rpl_sectors.title',
            'rpl_sectors.title_en',
            'rpl_sectors.translations',
            'rpl_sectors.created_at',
            'rpl_sectors.updated_at',
            'rpl_sectors.deleted_at',
        ]);

        if (is_numeric($id)) {
            $rplSectorBuilder->where('rpl_sectors.id', $id);
        }

        return $rplSectorBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplSector
     */
    public function store(array $data): RplSector
    {
        $rplSector = app()->make(RplSector::class);
        $rplSector->fill($data);
        $rplSector->save();
        return $rplSector;
    }

    /**
     * @param RplSector $rplSector
     * @param array $data
     * @return RplSector
     */
    public function update(RplSector $rplSector, array $data): RplSector
    {
        $rplSector->fill($data);
        $rplSector->save();
        return $rplSector;
    }

    /**
     * @param RplSector $rplSector
     * @return bool
     */
    public function destroy(RplSector $rplSector): bool
    {
        return $rplSector->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $customMessage = [
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        $rules = [
            'permission_sub_group_id' => [
                Rule::requiredIf(function () use ($id) {
                    return is_null($id);
                }),
                'nullable',
                'int'
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
                'exists:countries,id,deleted_at,NULL'
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
