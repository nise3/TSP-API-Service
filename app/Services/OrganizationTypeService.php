<?php


namespace App\Services;

use App\Models\OrganizationType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class OrganizationTypeService
 * @package App\Services
 */
class OrganizationTypeService
{
    /**
     * @param Request $request
     * @return array
     */
    public function getAllOrganizationType(Request $request): array
    {

        $paginate_link = [];
        $page = [];
        $startTime = Carbon::now();

        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';


        $organizationTypes = OrganizationType::select([
            'organization_types.id as id',
            'organization_types.title_en',
            'organization_types.title_bn',
            'organization_types.is_government',
            'organization_types.row_status'
        ])->orderBy('organization_types.id', $order);

        if (!empty($titleEn)) {
            $organizationTypes->where('organization_types.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $organizationTypes->where('organization_types.title_bn', 'like', '%' . $titleBn . '%');
        }
        if ($paginate) {
            $organizationTypes = $organizationTypes->paginate(10);
            $paginate_data = (object)$organizationTypes->toArray();
            $page = [
                "size" => $paginate_data->per_page,
                "total_element" => $paginate_data->total,
                "total_page" => $paginate_data->last_page,
                "current_page" => $paginate_data->current_page
            ];
            $paginate_link = $paginate_data->links;
        } else {
            $organizationTypes = $organizationTypes->get();
        }
        $data = [];
        foreach ($organizationTypes as $organizationType) {
            $_links['read'] = route('api.v1.organization-types.read', ['id' => $organizationType->id]);
            $_links['update'] = route('api.v1.organization-types.update', ['id' => $organizationType->id]);
            $_links['delete'] = route('api.v1.organization-types.destroy', ['id' => $organizationType->id]);
            $organizationType['_links'] = $_links;
            $data[] = $organizationType->toArray();

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
                    '_link' => route('api.v1.organization-types.get-list')
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
    public function getOneOrganizationType($id): array
    {
        $startTime = Carbon::now();
        $links = [];
        $organizationType = OrganizationType::select([
            'organization_types.id as id',
            'organization_types.title_en',
            'organization_types.title_bn',
            'organization_types.is_government',
            'organization_types.row_status'
        ])->where('organization_types.row_status', '=', OrganizationType::ROW_STATUS_ACTIVE)
            ->where('organization_types.id', '=', $id);
        $organizationType = $organizationType->first();

        if (!empty($organizationType)) {
            $links = [
                'update' => route('api.v1.organization-types.update', ['id' => $organizationType->id]),
                'delete' => route('api.v1.organization-types.destroy', ['id' => $organizationType->id])
            ];
        }

        return [
            "data" => $organizationType ? $organizationType : [],
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
     * @return OrganizationType
     */
    public function store(array $data): OrganizationType
    {
        $organizationType = new OrganizationType();
        $organizationType->fill($data);
        $organizationType->save();
        return $organizationType;

    }

    /**
     * @param OrganizationType $organizationType
     * @param array $data
     * @return OrganizationType
     */
    public function update(OrganizationType $organizationType, array $data): OrganizationType
    {
        $organizationType->fill($data);
        $organizationType->save();
        return $organizationType;

    }

    /**
     * @param OrganizationType $organizationType
     * @return OrganizationType
     */
    public function destroy(OrganizationType $organizationType): OrganizationType
    {
        $organizationType->row_status = 99;
        $organizationType->save();
        return $organizationType;

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
            'is_government' => [
                'required',
                'boolean'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
            ],
        ];
        return Validator::make($request->all(), $rules);
    }

}
