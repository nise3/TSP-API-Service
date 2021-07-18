<?php


namespace App\Services;

use App\Models\Occupation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class OccupationService
 * @package App\Services
 */
class OccupationService
{
    /**
     * @param Request $request
     * @return array
     */
    public function getOccupationList(Request $request): array
    {
        $paginate_link = [];
        $page = [];
        $startTime = Carbon::now();

        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $occupations = Occupation::select([
            'occupations.id',
            'occupations.title_en',
            'occupations.title_bn',
            'occupations.row_status',
            'job_sectors.title_en as job_sectors_title',
        ]);
        $occupations->join('job_sectors', 'occupations.job_sector_id', '=', 'job_sectors.id')
            ->orderBy('occupations.id', $order);
        if (!empty($titleEn)) {
            $occupations->where('occupations.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $occupations->where('occupations.title_en', 'like', '%' . $titleBn . '%');
        }
        if ($paginate) {
            $occupations = $occupations->paginate(10);
            $paginate_data = (object)$occupations->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link = $paginate_data->links;
        } else {
            $occupations = $occupations->get();
        }

        $data = [];

        foreach ($occupations as $occupation) {
            $_links['read'] = route('api.v1.occupations.read', ['id' => $occupation->id]);
            $_links['update'] = route('api.v1.occupations.update', ['id' => $occupation->id]);
            $_links['delete'] = route('api.v1.occupations.destroy', ['id' => $occupation->id]);
            $district['_links'] = $_links;
            $data[] = $occupation->toArray();

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
                'search' => [
                    'parameters' => [
                        'title_en',
                        'title_bn'
                    ],
                    '_link' => route('api.v1.occupations.get-list')
                ]
            ],
            "_page" => $page,
            "_order" => $order
        ];

    }

    /**
     * @param $id
     * @return array
     */
    public function getOneOccupation($id)
    {
        $startTime = Carbon::now();
        $links = [];
        $occupation = Occupation::select([
            'occupations.id',
            'occupations.title_en',
            'occupations.title_bn',
            'occupations.row_status',
            'job_sectors.title_en as job_sectors_title',
        ]);
        $occupation->join('job_sectors', 'occupations.job_sector_id', '=', 'job_sectors.id')
            ->where('occupations.row_status', '=', Occupation::ROW_STATUS_ACTIVE)
            ->where('occupations.id', '=', $id);


        $occupation = $occupation->first();

        if (!empty($occupation)) {
            $links['update'] = route('api.v1.occupations.update', ['id' => $occupation->id]);
            $links['delete'] = route('api.v1.occupations.destroy', ['id' => $occupation->id]);
        }

        return [
            "data" => $occupation ? $occupation : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links
        ];

    }


    /**
     * @param array $data
     * @return Occupation
     */
    public function store(array $data): Occupation
    {
        $occupation = new Occupation();
        $occupation->fill($data);
        $occupation->save();
        return $occupation;


    }

    /**
     * @param Occupation $occupation
     * @param array $data
     * @return Occupation
     */
    public function update(Occupation $occupation, array $data): Occupation
    {

        $occupation->fill($data);
        $occupation->save();
        return $occupation;


    }

    /**
     * @param Occupation $occupation
     * @return Occupation
     */
    public function destroy(Occupation $occupation): Occupation
    {

        $occupation->row_status = 99;
        $occupation->save();
        return $occupation;


    }
    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request, $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title_en' => [
                'max:191',
                'required',
                'string'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:191',
            ],

            'job_sector_id' => [
                'required',
                'exists:job_sectors,id'
            ],
            'row_status' => [
                Rule::requiredIf(function () use ($id) {
                    return !empty($id);
                }),
                'int',
                'exists:row_status,code',
            ],
        ];

        return Validator::make($request->all(), $rules);
    }

}
