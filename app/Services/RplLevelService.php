<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\RplApplication;
use App\Models\RplLevel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RplLevelService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getRplLevelList(array $request, Carbon $startTime, bool $isPublicApi = false): array
    {
        $rplSectorId = $request['rpl_sector_id'] ?? "";
        $rplOccupationId = $request['rpl_occupation_id'] ?? "";
        $youthId = $request['youth_id'] ?? "";
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var RplLevel|Builder $rplLevelBuilder */
        $rplLevelBuilder = RplLevel::select([
            'rpl_levels.id',
            'rpl_levels.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',
            'rpl_levels.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',
            'rpl_levels.title',
            'rpl_levels.title_en',
            'rpl_levels.translations',
            'rpl_levels.sequence_order',
            'rpl_levels.created_at',
            'rpl_levels.updated_at',
            'rpl_levels.deleted_at',
        ]);

        if (!$isPublicApi) {
            $rplLevelBuilder->acl();
        }

        $rplLevelBuilder->orderBy('rpl_levels.id', $order);

        $rplLevelBuilder->join('rpl_sectors', function ($join) {
            $join->on('rpl_levels.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $rplLevelBuilder->join('rpl_occupations', function ($join) {
            $join->on('rpl_levels.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        if (!empty($titleEn)) {
            $rplLevelBuilder->where('rpl_levels.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $rplLevelBuilder->where('rpl_levels.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($rplSectorId)) {
            $rplLevelBuilder->where('rpl_levels.rpl_sector_id', $rplSectorId);
        }

        if (is_numeric($rplOccupationId)) {
            $rplLevelBuilder->where('rpl_levels.rpl_occupation_id', $rplOccupationId);
        }
        if (is_numeric($youthId) && is_numeric($rplOccupationId)) {
            $rplLevelValidIds = RplApplication::where('youth_id', $youthId)
                ->where('rpl_occupation_id', $rplOccupationId)
                ->where('application_status', '<=', RplApplication::APPLICATION_STATUS_APPLICATION_SUBMITTED)
                ->pluck('rpl_level_id')->toArray();

            $rplCompletedLevelIds = RplApplication::where('youth_id', $youthId)
                ->where('rpl_occupation_id', $rplOccupationId)
                ->where('application_status', RplApplication::APPLICATION_STATUS_ASSESSMENT_COMPLETED)
                ->pluck('rpl_level_id')->toArray();

            $validSequenceOrderLevelId = count($rplCompletedLevelIds) > 0 ? $rplCompletedLevelIds[count($rplCompletedLevelIds)-1] : 0;

            $rplLevelSeqIds = RplLevel::where('rpl_occupation_id', $rplOccupationId)
                ->where('id', $validSequenceOrderLevelId)
                ->pluck('sequence_order')->toArray();

            $validSequenceOrder = count($rplLevelSeqIds) > 0 ? $rplLevelSeqIds[count($rplLevelSeqIds)-1] : 0;

            $rplLevelInvalidIds = RplLevel::where('rpl_occupation_id', $rplOccupationId)
                ->where('sequence_order', '>', $validSequenceOrder+1)->orwhere('sequence_order', '<=', $validSequenceOrder)->get()->toArray();

            if (count($rplLevelInvalidIds) > 0) {
                $rplLevelBuilder
                    ->selectRaw("(CASE WHEN sequence_order>? OR sequence_order<=? THEN 0 ELSE 1 END) as eligible", [$validSequenceOrder+1,$validSequenceOrder]);
            } else if (count($rplLevelValidIds) > 0) {
                $rplLevelBuilder//->selectRaw('1 as eligible');
                    ->selectRaw("(CASE WHEN rpl_levels.id = ? THEN 1 ELSE 0 END) as eligible", [$rplLevelValidIds[0]]);
            } else {
                $rplLevelBuilder->selectRaw("(CASE WHEN rpl_levels.sequence_order = 1 THEN 1 ELSE 0 END) as eligible");
            }
        }


        /** @var Collection $rplLevels */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $rplLevels = $rplLevelBuilder->paginate($pageSize);
            $paginateData = (object)$rplLevels->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rplLevels = $rplLevelBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $rplLevels->toArray()['data'] ?? $rplLevels->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplLevel
     */
    public function getOneRplLevel(int $id): RplLevel
    {
        /** @var RplLevel|Builder $rplLevelBuilder */
        $rplLevelBuilder = RplLevel::select([
            'rpl_levels.id',
            'rpl_levels.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',
            'rpl_levels.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',
            'rpl_levels.title',
            'rpl_levels.title_en',
            'rpl_levels.translations',
            'rpl_levels.sequence_order',
            'rpl_levels.created_at',
            'rpl_levels.updated_at',
            'rpl_levels.deleted_at',
        ]);

        if (is_numeric($id)) {
            $rplLevelBuilder->where('rpl_levels.id', $id);
        }

        $rplLevelBuilder->join('rpl_sectors', function ($join) {
            $join->on('rpl_levels.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $rplLevelBuilder->join('rpl_occupations', function ($join) {
            $join->on('rpl_levels.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        return $rplLevelBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplLevel
     */
    public function store(array $data): RplLevel
    {
        $rplLevel = app()->make(RplLevel::class);
        $rplLevel->fill($data);
        $rplLevel->save();
        return $rplLevel;
    }

    /**
     * @param RplLevel $rplLevel
     * @param array $data
     * @return RplLevel
     */
    public function update(RplLevel $rplLevel, array $data): RplLevel
    {
        $rplLevel->fill($data);
        $rplLevel->save();
        return $rplLevel;
    }

    /**
     * @param RplLevel $rplLevel
     * @return bool
     */
    public function destroy(RplLevel $rplLevel): bool
    {
        return $rplLevel->delete();
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
            'rpl_occupation_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_occupations,id,deleted_at,NULL',
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
            'sequence_order' => [
                'required',
                'int',
                'min:1',
                Rule::unique('rpl_levels', 'sequence_order')
                    ->ignore($id)
                    ->where(function (\Illuminate\Database\Query\Builder $query) use ($data) {
                        return $query->where('rpl_occupation_id', $data['rpl_occupation_id'])->whereNull('deleted_at');
                    })
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
            'rpl_occupation_id' => 'nullable|int',
            'rpl_sector_id' => 'nullable|int',
            'youth_id' => 'nullable|int',
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
