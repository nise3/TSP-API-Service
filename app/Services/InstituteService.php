<?php


namespace App\Services;


use App\Models\Institute;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\RequiredIf;


class InstituteService
{
    public function getInstituteList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        $institutes = Institute::select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.address',
            'institutes.domain',
            'institutes.created_at',
            'institutes.updated_at'
        ]);

        $institutes->orderBy('institutes.id', $order);

        if (!empty($titleEn)) {
            $institutes->where('human_resource_templates.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $institutes->where('human_resource_templates.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $institutes = $institutes->paginate(10);
            $paginate_data = (object)$institutes->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $institutes = $institutes->get();
        }

        $data = [];
        foreach ($institutes as $institute) {
            $_links['read'] = route('api.v1.institutes.read', ['id' => $institute->id]);
            $_links['update'] = route('api.v1.institutes.update', ['id' => $institute->id]);
            $_links['delete'] = route('api.v1.institutes.destroy', ['id' => $institute->id]);
            $institute['_links'] = $_links;
            $data[] = $institute->toArray();
        }

        return [
            "data" => $data,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => [
                'paginate' => $paginate_link,

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

    public function getOneInstitute($id): array
    {
        $startTime = Carbon::now();
        $institute = Institute::select([
            'institutes.id as id',
            'institutes.title_en',
            'institutes.title_bn',
            'institutes.code',
            'institutes.address',
            'institutes.domain',
            'institutes.created_at',
            'institutes.updated_at',
        ]);


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
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];

    }

    public function validator(Request $request, $id = null): Validator
    {
        $rules = [
            'title_en' => ['required', 'string', 'max:191'],
            'title_bn' => ['required', 'string', 'max:191'],
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
                new RequiredIf($id == null),
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:500',
                //'dimensions:width=80,height=80'
            ]
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
    public function destroy(Institute  $institute): Institute
    {
        $institute->row_status = Institute::ROW_STATUS_DELETED;
        $institute->save();
        return $institute;
    }

}
