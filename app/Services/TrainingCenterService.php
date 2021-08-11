<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

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
        $paginateLink = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var TrainingCenter|Builder $trainingCentersBuilder */
        $trainingCentersBuilder = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'training_centers.institute_id',
            'institutes.title_en as institute_name',
            'training_centers.branch_id',
            'branches.title_en as branch_name',
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
        if ($paginate) {
            $trainingCenters = $trainingCentersBuilder->paginate(10);
            $paginateData = (object)$trainingCenters->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $trainingCenters = $trainingCentersBuilder->get();
        }

        $data = [];
        foreach ($trainingCenters as $trainingCenter) {
            /** @var TrainingCenter $trainingCenter */
            $links['read'] = route('api.v1.training-centers.read', ['id' => $trainingCenter->id]);
            $links['update'] = route('api.v1.training-centers.update', ['id' => $trainingCenter->id]);
            $links['delete'] = route('api.v1.training-centers.destroy', ['id' => $trainingCenter->id]);
            $trainingCenter['_links'] = $links;
            $data[] = $trainingCenter->toArray();
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
                    '_link' => route('api.v1.training-centers.get-list')
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
            'training_centers.row_status',
            'training_centers.created_by',
            'training_centers.updated_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCenterBuilder->join('institutes', 'training_centers.institute_id', '=', 'institutes.id');
        $trainingCenterBuilder->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');
        $trainingCenterBuilder->where('training_centers.id', '=', $id);

        /** @var TrainingCenter $trainingCenterBuilder */
        $trainingCenter = $trainingCenterBuilder->first();

        $links = [];
        if (!empty($programme)) {
            $links['update'] = route('api.v1.training-centers.update', ['id' => $trainingCenter->id]);
            $links['delete'] = route('api.v1.training-centers.destroy', ['id' => $trainingCenter->id]);
        }

        return [
            "data" => $trainingCenter ?: null,
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
            'address' => ['nullable', 'string', 'max:1000'],
            'google_map_src' => ['nullable', 'string'],
            'row_status' => [
                'required_if:' . $id . ',==,null',
                Rule::in([TrainingCenter::ROW_STATUS_ACTIVE, TrainingCenter::ROW_STATUS_INACTIVE]),
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
}
