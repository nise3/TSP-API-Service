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
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Institute|Builder $instituteBuilder*/
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
        if ($paginate) {
            $institutes = $instituteBuilder->paginate($limit);
            $paginateData = (object)$institutes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $institutes = $instituteBuilder->get();
        }
        $response['order']=$order;
        $response['data']=$institutes->toArray()['data'] ?? $institutes->toArray();

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
            'institutes.domain',
            'institutes.address',
            'institutes.google_map_src',
            'institutes.row_status',
            'institutes.created_by',
            'institutes.updated_by',
            'institutes.created_at',
            'institutes.updated_at',
        ]);

        $instituteBuilder->where('institutes.id', $id);
        /** @var Institute $instituteBuilder */
        $institute = $instituteBuilder->first();

        return [
            "data" => $institute ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "started" => $startTime->format('H i s'),
                "finished" => Carbon::now()->format('H i s'),
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
                'maxSize:512000',
                'mimes:png,jpg,jpeg',
                "dimensions:max_width=80,max_height=80"
            ],
            'is_training_center' => 'nullable|boolean',
            'training_center_name_en' => 'nullable|string|max: 191',
            'training_center_name_bn' => 'nullable|string|max: 191',
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        $messages = [
            'logo.dimensions' => 'Please upload 80x80 size of image',
            'logo.max' => 'Please upload maximum 500kb size of image',
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
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
/*        if($data['is_training_center']==true){
            $tData['institute_id'] = $institute->id;
            $tData['title_en'] = $data['training_center_name_en'];
            $tData['title_bn'] = $data['training_center_name_bn'];
            $trainingCenter = new TrainingCenter();
            $trainingCenter->fill($tData);
            $trainingCenter->save();
        }*/
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
}
