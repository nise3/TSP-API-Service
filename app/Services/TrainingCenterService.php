<?php


namespace App\Services;

use Illuminate\Http\Request;
use App\Models\TrainingCenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class TrainingCenterService
 * @package App\Services
 */
class TrainingCenterService
{
    /**
     * @param Request $request
     * @return array
     */
    public function getTrainingCenterList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $trainingCenters = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'institutes.title_en as institute_name',
            'training_centers.row_status',
            'branches.title_en as branch_name',
//            'users.name_en as training_center_created_by',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCenters->join('institutes', 'training_centers.institute_id', '=', 'institutes.id')
            ->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id');


        if (!empty($titleEn)) {
            $trainingCenters->where('training_centers.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $trainingCenters->where('training_centers.title_bn', 'like', '%' . $titleBn . '%');
        }


        if ($paginate) {
            $trainingCenters = $trainingCenters->paginate(10);
            $paginate_data = (object)$trainingCenters->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $trainingCenters = $trainingCenters->get();
        }
        $data = [];
        foreach ($trainingCenters as $trainingCenter) {
            $_links['read'] = route('api.v1.training-centers.read', ['id' => $trainingCenter->id]);
            $_links['update'] = route('api.v1.training-centers.update', ['id' => $trainingCenter->id]);
            $_links['delete'] = route('api.v1.training-centers.destroy', ['id' => $trainingCenter->id]);
            $programme['_links'] = $_links;
            $data[] = $trainingCenter->toArray();
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
                    '_link' => route('api.v1.training-centers.get-list')
                ],
            ],
            "_page" => $page,
            "_order" => $order
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneTrainingCenter($id): array
    {
        $startTime = Carbon::now();

        $trainingCenter = TrainingCenter::select([
            'training_centers.id as id',
            'training_centers.title_en',
            'training_centers.title_bn',
            'institutes.title_en as institute_name',
            'branches.title_en as branch_name',
            'training_centers.address',
            'training_centers.row_status',
            'training_centers.google_map_src',
            'training_centers.created_at',
            'training_centers.updated_at'
        ]);
        $trainingCenter->join('institutes', 'training_centers.institute_id', '=', 'institutes.id')
            ->leftJoin('branches', 'training_centers.branch_id', '=', 'branches.id')
            ->where('training_centers.id',$id)
            ->where('training_centers.row_status', '=', TrainingCenter::ROW_STATUS_ACTIVE);
        $trainingCenter = $trainingCenter->first();
        $links = [];

        if (!empty($programme)) {
            $_links['update'] = route('api.v1.training-centers.update', ['id' => $trainingCenter->id]);
            $_links['delete'] = route('api.v1.training-centers.destroy', ['id' => $trainingCenter->id]);
        }

        return [
            "data" => $trainingCenter ?: null,
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
        return $trainingCenter;
    }


    /**
     * @param Request $request
     * @return Validator
     */
    public function validator(Request $request): Validator
    {
        $rules = [
            'title_en' => 'required|string|max: 191',
            'title_bn' => 'required|string|max: 191',
            'institute_id' => 'required|int',
            'branch_id' => 'nullable|int',
            'address' => ['nullable', 'string', 'max:191'],
            'google_map_src' => ['nullable', 'string'],
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
