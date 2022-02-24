<?php

namespace App\Services;

use App\Models\RtoCountry;

use App\Traits\Scopes\SagaStatusGlobalScope;
use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Contracts\Validation\Validator;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;


class RtoCountryService
{


    public function getRtoCountryList(array $request, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";


        $rtoBuilder = RtoCountry::select(
            [
                'rto_countries.country_id',

                'countries.title',
                'countries.title_en',
                'countries.code',
            ]
        );

        $rtoBuilder->leftJoin('countries',function ($join){
           $join->on("countries.id",'=',"rto_countries.country_id");
        });

        /** @var Collection $courses */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $countries = $rtoBuilder->paginate($pageSize);
            $paginateData = (object)$countries->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $countries = $rtoBuilder->get();
        }
        $response['data'] = $countries->toArray()['data'] ?? $countries->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }


    public function store(array $data): mixed
    {

        DB::table('rto_countries')->truncate();
        $countryIds = $data["country_ids"];
        foreach ($countryIds as $countryId){
            $countryArray["country_id"] = $countryId;
            $country = new RtoCountry();
            $country->fill($countryArray);
            $country->save();
        }
        return 1;
    }


    public function update(RtoCountry $country, array $data): RtoCountry
    {
        $country->fill($data);
        $country->save();

        return $country;
    }



    public function destroy(RtoCountry $country): bool
    {
        return $country->delete();
    }



    public function filterValidator(Request $request, $type = null): Validator
    {
        $requestData = $request->all();
        $rules = [
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
        ];
        return \Illuminate\Support\Facades\Validator::make($requestData, $rules);
    }

    public function validator(Request $request, int $id = Null): \Illuminate\Contracts\Validation\Validator
    {
        if ($request->filled('country_ids')) {
            $country_ids = is_array($request->get('country_ids')) ? $request->get('country_ids') : explode(',', $request->get('country_ids'));
            $request->offsetSet('country_ids', $country_ids);
        }

        $rules = [
            'country_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'country_ids.*' => [
                'required',
                'int',
                'exists:countries,id,deleted_at,NULL'
            ],
        ];


        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

}
