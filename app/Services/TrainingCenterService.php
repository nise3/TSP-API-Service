<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

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

        /** @var TrainingCenter|Builder $trainingCenters */
        $trainingCenters = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'institutes.title_en as institute_name',
            'institutes.id as institute_id',
            'training_centers.row_status',
            'branches.title_en as branch_name',
            'branches.id as branch_id',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCenters->join('institutes', 'training_centers.institute_id', '=', 'institutes.id');
        $trainingCenters->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');

        if (!empty($titleEn)) {
            $trainingCenters->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainingCenters->where('training_centers.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $trainingCenters = $trainingCenters->paginate(10);
            $paginateData = (object)$trainingCenters->toArray();
            $page = [
                "size" => $paginateData->per_page,
                "total_element" => $paginateData->total,
                "total_page" => $paginateData->last_page,
                "current_page" => $paginateData->current_page
            ];
            $paginateLink[] = $paginateData->links;
        } else {
            $trainingCenters = $trainingCenters->get();
        }

        $data = [];
        foreach ($trainingCenters as $trainingCenter) {
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
        /** @var TrainingCenter|Builder $trainingCenter */
        $trainingCenter = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'institutes.title_en as institute_name',
            'institutes.title_en as institute_name',
            'branches.title_en as branch_name',
            'branches.id as branch_id',
            'training_centers.address',
            'training_centers.row_status',
            'training_centers.google_map_src',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCenter->join('institutes', 'training_centers.institute_id', '=', 'institutes.id');
        $trainingCenter->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');
        $trainingCenter->where('training_centers.id', '=', $id);
        $trainingCenter = $trainingCenter->first();

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
     * @return TrainingCenter
     */
    public function destroy(TrainingCenter $trainingCenter): TrainingCenter
    {
        $trainingCenter->row_status = TrainingCenter::ROW_STATUS_DELETED;
        $trainingCenter->save();
        $trainingCenter->delete();
        return $trainingCenter;
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
