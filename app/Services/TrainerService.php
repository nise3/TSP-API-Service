<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrainerService
 * @package App\Services
 */
class TrainerService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainerList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::select([
            'trainers.id as id',
            'trainers.trainer_name_en',
            'trainers.trainer_name_bn',
            'trainers.institute_id',
            'trainers.training_center_id',
            'trainers.branch_id',
            'trainers.email',
            'trainers.date_of_birth as date_of_birth',
            'trainers.about_me',
            'trainers.gender as gender',
            'trainers.marital_status as marital_status',
            'trainers.religion as religion',
            'trainers.nationality as nationality',
            'trainers.nid',
            'trainers.passport_number as passport_number',
            'trainers.physical_disabilities_status as physical_disabilities_status',
            'trainers.freedom_fighter_status as freedom_fighter_status',
            'trainers.present_address_division_id as present_address_division_id',
            'trainers.present_address_district_id as present_address_district_id',
            'trainers.present_address_upazila_id as present_address_upazila_id',
            'trainers.present_house_address as present_house_address',
            'trainers.permanent_address_division_id as permanent_address_division_id',
            'trainers.permanent_address_district_id as permanent_address_district_id',
            'trainers.permanent_address_upazila_id as permanent_address_upazila_id',
            'trainers.permanent_house_address as permanent_house_address',
            'trainers.educational_qualification as educational_qualification',
            'trainers.skills as skills',
            'trainers.photo as photo',
            'trainers.signature as signature',
            'trainers.row_status',
            'trainers.created_at',
            'trainers.updated_at',
        ]);

        $trainerBuilder->orderBy('trainers.id', $order);

        if (!empty($titleEn)) {
            $trainerBuilder->where('trainers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainerBuilder->where('trainers.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $trainerBuilder */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $trainers = $trainerBuilder->paginate($limit);
            $paginateData = (object)$trainers->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainers = $trainerBuilder->get();
        }

        $response['order']=$order;
        $response['data']=$trainers->toArray()['data'] ?? $trainers->toArray();
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
    public function getOneTrainer(int $id, Carbon $startTime): array
    {
        /** @var Trainer|Builder $trainerBuilder */
        $trainerBuilder = Trainer::select([
            'trainers.id as id',
            'trainers.trainer_name_en',
            'trainers.trainer_name_bn',
            'trainers.institute_id',
            'trainers.training_center_id',
            'trainers.branch_id',
            'trainers.email',
            'trainers.date_of_birth as date_of_birth',
            'trainers.about_me as about_me',
            'trainers.gender as gender',
            'trainers.marital_status as marital_status',
            'trainers.religion as religion',
            'trainers.nationality as nationality',
            'trainers.nid as nid',
            'trainers.passport_number as passport_number',
            'trainers.physical_disabilities_status as physical_disabilities_status',
            'trainers.freedom_fighter_status as freedom_fighter_status',
            'trainers.present_address_division_id as present_address_division_id',
            'trainers.present_address_district_id as present_address_district_id',
            'trainers.present_address_upazila_id as present_address_upazila_id',
            'trainers.present_house_address as present_house_address',
            'trainers.permanent_address_division_id as permanent_address_division_id',
            'trainers.permanent_address_district_id as permanent_address_district_id',
            'trainers.permanent_address_upazila_id as permanent_address_upazila_id',
            'trainers.permanent_house_address as permanent_house_address',
            'trainers.educational_qualification as educational_qualification',
            'trainers.skills as skills',
            'trainers.photo as photo',
            'trainers.signature as signature',
            'trainers.row_status',
            'trainers.created_at',
            'trainers.updated_at',
        ]);

        /** @var Trainer $trainerBuilder */
        $trainer = $trainerBuilder->first();
        return [
            "data" => $trainer ?: [],
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
     * @return Trainer
     */
    public function store(array $data): Trainer
    {
        $trainer = new Trainer();
        $trainer->fill($data);
        $trainer->Save();
        return $trainer;
    }

    /**
     * @param Trainer $trainer
     * @param array $data
     * @return Trainer
     */
    public function update(Trainer $trainer, array $data): Trainer
    {
        $trainer->fill($data);
        $trainer->save();
        return $trainer;
    }

    /**
     * @param Trainer $trainer
     * @return bool
     */
    public function destroy(Trainer $trainer): bool
    {
        return $trainer->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $rules = [
            'trainer_name_en' => [
                'required',
                'string',
                'max:191'
            ],
            'trainer_name_bn' => [
                'required',
                'string',
                'max:1000'
            ],
            'institute_id' => [
                'nullable',
                'int',
                'exists:institutes,id'
            ],
            'branch_id' => [
                'nullable',
                'int',
                'exists:institutes,id'
            ],

            'trainer_registration_number' => [
                'nullable',
                'string',
                'unique:trainers,trainer_registration_number,' . $id
            ],
            'email' => [
                'nullable',
                'string',
                'unique:trainers,email,' . $id
            ],
            'mobile' => [
                'nullable',
                'string',
                'unique:trainers,mobile,' . $id
            ],
            'date_of_birth' => [
                'nullable',
                'date-time'
            ],
            'about_me' => [
                'nullable',
                'string'
            ],
            'gender' => [
                'nullable',
                'int'
            ],
            'marital_status' => [
                'nullable',
                'int'
            ],
            'religion' => [
                'nullable',
                'inr'
            ],
            'nationality' => [
                'nullable',
                'string'
            ],
            'nid' => [
                'nullable',
                'string'
            ],
            'passport_number' => [
                'nullable',
                'string'
            ],
            'physical_disabilities_status' => [
                'nullable',
                'int'
            ],
            'freedom_fighter_status' => [
                'nullable',
                'int'
            ],
            'present_address_division_id' => [
                'nullable',
                'int'
            ],
            'present_address_district_id' => [
                'nullable',
                'int'
            ],
            'present_address_upazila_id' => [
                'nullable',
                'int'
            ],
            'present_house_address' => [
                'nullable',
                'string'
            ],
            'permanent_address_division_id' => [
                'nullable',
                'int'
            ],
            'permanent_address_district_id' => [
                'nullable',
                'int'
            ],
            'permanent_address_upazila_id' => [
                'nullable',
                'int'
            ],
            'permanent_house_address' => [
                'nullable',
                'string'
            ],
            'educational_qualification' => [
                'nullable',
                'string'
            ],
            'skills' => [
                'nullable',
                'string'
            ],
            'photo' => [
                'nullable',
                'string'
            ],
            'signature' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}
