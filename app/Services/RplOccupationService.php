<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\RegisteredTrainingOrganization;
use App\Models\RplOccupation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RplOccupationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getRplOccupationList(array $request, Carbon $startTime, bool $isPublicApi = false): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rplSectorId = $request['rpl_sector_id'] ?? "";

        /** @var RplOccupation|Builder $rplOccupationBuilder */
        $rplOccupationBuilder = RplOccupation::select([
            'rpl_occupations.id',
            'rpl_occupations.title',
            'rpl_occupations.title_en',
            'rpl_occupations.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',
            'rpl_occupations.translations',
            'rpl_occupations.created_at',
            'rpl_occupations.updated_at',
            'rpl_occupations.deleted_at',
        ]);

        if(!$isPublicApi){
            $rplOccupationBuilder->acl();
        }

        $rplOccupationBuilder->orderBy('rpl_occupations.id', $order);

        $rplOccupationBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        if (!empty($titleEn)) {
            $rplOccupationBuilder->where('rpl_occupations.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $rplOccupationBuilder->where('rpl_occupations.title', 'like', '%' . $title . '%');
        }

        if (!empty($rplSectorId)) {
            $rplOccupationBuilder->where('rpl_occupations.rpl_sector_id', $rplSectorId);
        }

        /** @var Collection $rplOccupations */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $rplOccupations = $rplOccupationBuilder->paginate($pageSize);
            $paginateData = (object)$rplOccupations->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rplOccupations = $rplOccupationBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $rplOccupations->toArray()['data'] ?? $rplOccupations->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplOccupation
     */
    public function getOneRplOccupation(int $id): RplOccupation
    {
        /** @var RplOccupation|Builder $rplOccupationBuilder */
        $rplOccupationBuilder = RplOccupation::select([
            'rpl_occupations.id',
            'rpl_occupations.title',
            'rpl_occupations.title_en',
            'rpl_occupations.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',
            'rpl_occupations.translations',
            'rpl_occupations.created_at',
            'rpl_occupations.updated_at',
            'rpl_occupations.deleted_at',
        ]);

        if (is_numeric($id)) {
            $rplOccupationBuilder->where('rpl_occupations.id', $id);
        }

        $rplOccupationBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        return $rplOccupationBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplOccupation
     */
    public function store(array $data): RplOccupation
    {
        $rplOccupation = app()->make(RplOccupation::class);
        $rplOccupation->fill($data);
        $rplOccupation->save();
        return $rplOccupation;
    }

    /**
     * @param RplOccupation $rplOccupation
     * @param array $data
     * @return RplOccupation
     */
    public function update(RplOccupation $rplOccupation, array $data): RplOccupation
    {
        $rplOccupation->fill($data);
        $rplOccupation->save();
        return $rplOccupation;
    }

    /**
     * @param RplOccupation $rplOccupation
     * @return bool
     */
    public function destroy(RplOccupation $rplOccupation): bool
    {
        return $rplOccupation->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $rules = [
            'rpl_sector_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_sectors,id,deleted_at,NULL',
            ],
            'title' => [
                'required',
                'string',
                'max:500',
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'translations' => [
                'nullable',
                'array',
                'min:1'
            ],
            'translations.*' => [
                Rule::requiredIf(!empty($data['translations'])),
                'array',
                'min:1'
            ],
            'translations.*.title' => [
                Rule::requiredIf(!empty($data['translations']))
            ]
        ];
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }


    /**
     * @param Request $request
     * @return Validator
     */
    public function filterValidator(Request $request): Validator
    {
        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }
        $customMessage = [
            'order.in' => 'Order must be either ASC or DESC. [30000]',
            //'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'country_id' => 'nullable|int',
            'rpl_sector_id' => 'nullable|int',
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
//            'row_status' => [
//                "nullable",
//                "int",
//                Rule::in(Institute::ROW_STATUSES),
//            ],
        ], $customMessage);
    }
}
