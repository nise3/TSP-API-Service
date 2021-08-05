<?php

namespace App\Services;

use App\Models\Institute;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;


/**
 * Class InstituteService
 * @package App\Services
 */
class InstituteService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getInstituteList(Request $request, Carbon $startTime): array
    {
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Institute|Builder $institutes */
        $institutes = Institute::select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.address',
            'institutes.domain',
            'institutes.google_map_src',
            'institutes.logo',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.config',
            'institutes.created_at',
            'institutes.updated_at',
        ]);
        $institutes->orderBy('institutes.id', $order);

        if (!empty($titleEn)) {
            $institutes->where('institutes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $institutes->where('institutes.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $institutes = $institutes->paginate(10);
            $paginateData = (object)$institutes->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $institutes = $institutes->get();
        }

        $data = [];
        foreach ($institutes as $institute) {
            $links['read'] = route('api.v1.institutes.read', ['id' => $institute->id]);
            $links['update'] = route('api.v1.institutes.update', ['id' => $institute->id]);
            $links['delete'] = route('api.v1.institutes.destroy', ['id' => $institute->id]);
            $institute['_links'] = $links;
            $data[] = $institute->toArray();
        }

        return [
            "data" => $data ?: null,
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
                    '_link' => route('api.v1.institutes.get-list')
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
    public function getOneInstitute(int $id, Carbon $startTime): array
    {
        /** @var Institute|Builder $institute */
        $institute = Institute::select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.address',
            'institutes.domain',
            'institutes.google_map_src',
            'institutes.logo',
            'institutes.primary_phone',
            'institutes.phone_numbers',
            'institutes.primary_mobile',
            'institutes.mobile_numbers',
            'institutes.email',
            'institutes.config',
            'institutes.created_at',
            'institutes.updated_at',
        ]);

        $institute->where('institutes.id', $id);
        $institute = $institute->first();

        $links = [];
        if (!empty($institute)) {
            $links['update'] = route('api.v1.institutes.update', ['id' => $id]);
            $links['delete'] = route('api.v1.institutes.destroy', ['id' => $id]);
        }

        return [
            "data" => $institute ?: null,
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
     * @param Request $request
     * @param null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $rules = [
            'title_en' => ['required', 'string', 'max:191'],
            'title_bn' => ['required', 'string', 'max:1000'],
            'code' => ['required', 'string', 'max:191', 'unique:institutes,code,' . $id],
            'domain' => [
                'required',
                'string',
                'regex:/^(http|https):\/\/[a-zA-Z-\-\.0-9]+$/',
                'max:191',
                'unique:institutes,domain,' . $id
            ],
            'address' => ['nullable', 'string', 'max:191'],
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
                'max:191',
            ],
            'row_status' => [
                'required_if:' . $id . ',==,null',
                Rule::in([Institute::ROW_STATUS_ACTIVE, Institute::ROW_STATUS_INACTIVE]),
            ],
        ];
        $messages = [
            'logo.dimensions' => 'Please upload 80x80 size of image',
            'logo.max' => 'Please upload maximum 500kb size of image',
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
    }

    /**
     * @param array $data
     * @return Institute
     */
    public function store(array $data): Institute
    {
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
        $institute->fill($data);
        $institute->save();
        return $institute;
    }

    /**
     * @param Institute $institute
     * @return Institute
     */
    public function destroy(Institute $institute): Institute
    {
        $institute->row_status = Institute::ROW_STATUS_DELETED;
        $institute->save();
        $institute->delete();
        return $institute;
    }
}
