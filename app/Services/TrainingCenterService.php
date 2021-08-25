<?php

namespace App\Services;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrainingCenterService
 * @package App\Services
 */
class TrainingCenterService
{
    /**
     * @param Request $request
     * @param Carbon $startTime
     * @return array
     */
    public function getTrainingCenterList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $rowStatus=$request->query('row_status');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.loc_division_id',
            'training_centers.loc_district_id',
            'training_centers.loc_upazila_id',
            'training_centers.institute_id',
            'institutes.title_en as institute_name',
            'training_centers.branch_id',
            'branches.title_en as branch_name',
            'training_centers.address',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCentersBuilder->join('institutes', 'training_centers.institute_id', '=', 'institutes.id');
        $trainingCentersBuilder->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');

        $trainingCentersBuilder->join("institutes",function($join) use($rowStatus){
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if(!is_null($rowStatus)){
                $join->where('institutes.row_status',$rowStatus);
            }
        });
        $trainingCentersBuilder->join("branches",function($join) use($rowStatus){
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if(!is_null($rowStatus)){
                $join->where('branches.row_status',$rowStatus);
            }
        });

        $trainingCentersBuilder->orderBy('training_centers.id', $order);

        if(!is_null($rowStatus)){
            $trainingCentersBuilder->where('training_centers.row_status',$rowStatus);
        }

        if (!empty($titleEn)) {
            $trainingCentersBuilder->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainingCentersBuilder->where('training_centers.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $trainingCentersBuilder */
        if (!is_null($paginate) || !is_null($limit)) {
            $limit = $limit ?: 10;
            $trainingCenters = $trainingCentersBuilder->paginate($limit);
            $paginateData = (object)$trainingCenters->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenters = $trainingCentersBuilder->get();
        }

        $response['order']=$order;
        $response['data']=$trainingCenters->toArray()['data'] ?? $trainingCenters->toArray();
        $response['response_status']= [
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
    public function getOneTrainingCenter(int $id, Carbon $startTime): array
    {
        /** @var TrainingCenter|Builder $trainingCenterBuilder */
        $trainingCenterBuilder = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.institute_id',
            'institutes.title_en as institute_name',
            'training_centers.branch_id',
            'branches.title_en as branch_name',
            'training_centers.center_location_type',
            'training_centers.address',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCenterBuilder->join("institutes",function($join){
            $join->on('training_centers.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });
        $trainingCenterBuilder->join("branches",function($join) {
            $join->on('training_centers.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
        });

        $trainingCenterBuilder->where('training_centers.id', '=', $id);

        /** @var TrainingCenter $trainingCenterBuilder */
        $trainingCenter = $trainingCenterBuilder->first();


        return [
            "data" => $trainingCenter ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param array $data
     * @return TrainingCenter
     */
    public function store(array $data): TrainingCenter
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $trainingCenter = new TrainingCenter();
        $trainingCenter->fill($data);
        $trainingCenter->Save();
        return $trainingCenter;
    }

    /**
     * @param TrainingCenter $trainingCenter
     * @param array $data
     * @return TrainingCenter
     */
    public function update(TrainingCenter $trainingCenter, array $data): TrainingCenter
    {
        if (!empty($data['google_map_src'])) {
            $data['google_map_src'] = $this->parseGoogleMapSrc($data['google_map_src']);
        }
        $trainingCenter->fill($data);
        $trainingCenter->Save();
        return $trainingCenter;
    }

    /**
     * @param TrainingCenter $trainingCenter
     * @return bool
     */
    public function destroy(TrainingCenter $trainingCenter): bool
    {
        return $trainingCenter->delete();
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Validator
     */
    public function validator(Request $request, $id = null): Validator
    {
        $rules = [
            'title_en' => 'required|string|max: 191',
            'title_bn' => 'required|string|max: 1000',
            'institute_id' => 'required|int|exists:institutes,id',
            'branch_id' => 'nullable|int|exists:branches,id',
            'center_location_type' => 'nullable|int',
            'address' => ['nullable', 'string', 'max:1000'],
            'google_map_src' => ['nullable', 'string'],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }

    /**
     * @param string|null $googleMapSrc
     * @return string
     */
    public function parseGoogleMapSrc(?string $googleMapSrc): ?string
    {
        if (!empty($googleMapSrc) && preg_match('/src="([^"]+)"/', $googleMapSrc, $match)) {
            $googleMapSrc = $match[1];
        }
        return $googleMapSrc;
    }

    public function getTrainingCenterTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::onlyTrashed()->select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.institute_id',
            'institutes.title_en as institute_name',
            'training_centers.branch_id',
            'branches.title_en as branch_name',
            'training_centers.address',
            'training_centers.address',
            'training_centers.google_map_src',
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCentersBuilder->join('institutes', 'training_centers.institute_id', '=', 'institutes.id');
        $trainingCentersBuilder->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');

        if (!empty($titleEn)) {
            $trainingCentersBuilder->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainingCentersBuilder->where('training_centers.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $trainingCentersBuilder */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $trainingCenters = $trainingCentersBuilder->paginate($limit);
            $paginateData = (object)$trainingCenters->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $trainingCenters = $trainingCentersBuilder->get();
        }

        $response['order']=$order;
        $response['data']=$trainingCenters->toArray()['data'] ?? $trainingCenters->toArray();
        $response['response_status']= [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    public function restore(TrainingCenter $trainingCenter): bool
    {
        return $trainingCenter->restore();
    }

    public function forceDelete(TrainingCenter $trainingCenter): bool
    {
        return $trainingCenter->forceDelete();
    }
}
