<?php


namespace App\Services;


use App\Models\JobSector;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class JobSectorService
 * @package App\Services
 */
class JobSectorService
{

    /**
     * @param Request $request
     * @return array
     */
    public function getJobsectorList(Request $request): array
    {
        $startTime = Carbon::now();
        $paginate_link = [];
        $page = [];
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';
        $jobSectors = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
                'job_sectors.row_status',
            ]
        )->orderBy('job_sectors.id', $order);
        if (!empty($titleEn)) {
            $jobSectors->where('$jobSectors.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $jobSectors->where('job_sectors.title_bn', 'like', '%' . $titleBn . '%');
        }

        if ($paginate) {
            $jobSectors = $jobSectors->paginate(10);
            $paginate_data = (object)$jobSectors->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link[] = $paginate_data->links;
        } else {
            $jobSectors = $jobSectors->get();
        }
        $data = [];

        foreach ($jobSectors as $jobSector) {
            $_links['read'] = route('api.v1.job-sectors.read', ['id' => $jobSector->id]);
            $_links['edit'] = route('api.v1.job-sectors.update', ['id' => $jobSector->id]);
            $_links['delete'] = route('api.v1.job-sectors.destroy', ['id' => $jobSector->id]);
            $_link['_links'] = $_links;
            $data[] = $jobSector->toArray();
        }


        $response = [
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
                    '_link' => route('api.v1.job-sectors.get-list')

                ],

            ],

            "_page" => $page,
            "_order" => $order
        ];

        return $response;

    }

    /**
     * @param $id
     * @return array
     */
    public function getOneJobSecotor($id): array
    {
        $startTime = Carbon::now();


        $jobSector = JobSector::select(
            [
                'job_sectors.id',
                'job_sectors.title_en',
                'job_sectors.title_bn',
                'job_sectors.row_status',
            ]
        )->where('job_sectors.row_status', '=', JobSector::ROW_STATUS_ACTIVE)
            ->where('job_sectors.id', '=', $id);

        $jobSector = $jobSector->first();

        $links = [];
        if (!empty($jobSector)) {
            $links['update'] = route('api.v1.job-sectors.update', ['id' => $id]);
            $links['delete'] = route('api.v1.job-sectors.destroy', ['id' => $id]);
        }
        $response = [
            "data" => $jobSector ? $jobSector : null,
            "_response_status" => [
                "success" => true,
                "code" => JsonResponse::HTTP_OK,
                "message" => "Job finished successfully.",
                "started" => $startTime,
                "finished" => Carbon::now(),
            ],
            "_links" => $links,
        ];
        return $response;

    }

    /**
     * @param array $data
     * @return JobSector
     */
    public function store(array $data): JobSector
    {
        $jobsector = new JobSector();
        $jobsector->fill($data);
        $jobsector->save();

        return $jobsector;
    }

    /**
     * @param JobSector $JobSector
     * @param array $data
     * @return JobSector
     */
    public function update(JobSector $JobSector, array $data): JobSector

    {
        $JobSector->fill($data);
        $JobSector->save();
        return $JobSector;
    }

    /**
     * @param JobSector $JobSector
     * @return JobSector
     */
    public function destroy(JobSector $JobSector): JobSector
    {
        $JobSector->row_status = 99;
        $JobSector->save();
        return $JobSector;
    }


    /**
     * @param Request $request
     * @param null $id
     * @return mixed
     */

    public function validator(Request $request, $id = null)
    {
        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:191',
            ],
            'title_bn' => [
                'required',
                'string',
                'max: 191',
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([JobSector::ROW_STATUS_ACTIVE, JobSector::ROW_STATUS_INACTIVE]),
            ],
        ];
        return Validator::make($request->all(), $rules);
    }

}
