<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\RtoBatch;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RtoBatchService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getRtoBatchList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $rplOccupationId = $request['rpl_occupation_id'] ?? "";
        $rplSectorId = $request['rpl_sector_id'] ?? "";
        $rplLevelId = $request['rpl_level_id'] ?? "";

        /** @var RtoBatch|Builder $rtoBatchBuilder */
        $rtoBatchBuilder = RtoBatch::select([
            'rto_batches.id',
            'rto_batches.title',
            'rto_batches.title_en',

            'rto_batches.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_occupations.rpl_sector_id as rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'rto_batches.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'rto_batches.created_at',
            'rto_batches.updated_at',
            'rto_batches.deleted_at',
        ]);

        $rtoBatchBuilder->orderBy('rto_batches.id', $order);

        $rtoBatchBuilder->join('rpl_occupations', function ($join){
            $join->on('rto_batches.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $rtoBatchBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $rtoBatchBuilder->join('rpl_levels', function ($join){
            $join->on('rto_batches.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        if (!empty($titleEn)) {
            $rtoBatchBuilder->where('rto_batches.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $rtoBatchBuilder->where('rto_batches.title', 'like', '%' . $title . '%');
        }
        if (!empty($rplOccupationId)) {
            $rtoBatchBuilder->where('rto_batches.rpl_occupation_id', $rplOccupationId);
        }
        if (!empty($rplSectorId)) {
            $rtoBatchBuilder->where('rpl_occupations.rpl_sector_id', $rplSectorId);
        }
        if (!empty($rplLevelId)) {
            $rtoBatchBuilder->where('rto_batches.rpl_level_id', $rplLevelId);
        }

        /** @var Collection $rtoBatches */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $rtoBatches = $rtoBatchBuilder->paginate($pageSize);
            $paginateData = (object)$rtoBatches->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $rtoBatches = $rtoBatchBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $rtoBatches->toArray()['data'] ?? $rtoBatches->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RtoBatch
     */
    public function getOneRtoBatch(int $id): RtoBatch
    {
        /** @var RtoBatch|Builder $rtoBatchBuilder */
        $rtoBatchBuilder = RtoBatch::select([
            'rto_batches.id',
            'rto_batches.title',
            'rto_batches.title_en',

            'rto_batches.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_occupations.rpl_sector_id as rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'rto_batches.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'rto_batches.created_at',
            'rto_batches.updated_at',
            'rto_batches.deleted_at',
        ]);

        if (is_numeric($id)) {
            $rtoBatchBuilder->where('rto_batches.id', $id);
        }

        $rtoBatchBuilder->join('rpl_occupations', function ($join){
            $join->on('rto_batches.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $rtoBatchBuilder->join('rpl_sectors', function ($join){
            $join->on('rpl_occupations.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $rtoBatchBuilder->join('rpl_levels', function ($join){
            $join->on('rto_batches.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        return $rtoBatchBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RtoBatch
     */
    public function store(array $data): RtoBatch
    {
        $rtoBatch = app()->make(RtoBatch::class);
        $rtoBatch->fill($data);
        $rtoBatch->save();
        return $rtoBatch;
    }

    /**
     * @param RtoBatch $rtoBatch
     * @param array $data
     * @return RtoBatch
     */
    public function update(RtoBatch $rtoBatch, array $data): RtoBatch
    {
        $rtoBatch->fill($data);
        $rtoBatch->save();
        return $rtoBatch;
    }

    /**
     * @param RtoBatch $rtoBatch
     * @return bool
     */
    public function destroy(RtoBatch $rtoBatch): bool
    {
        return $rtoBatch->delete();
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
            'title' => [
                'required',
                'string',
                'max:600',
            ],
            'title_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'institute_id' => [
                'required',
                'int',
                'min:1',
                'exists:institutes,id,deleted_at,NULL',
            ],
            'rpl_occupation_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_occupations,id,deleted_at,NULL',
            ],
            'rpl_level_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_levels,id,deleted_at,NULL',
            ],
            'assessor_id' => [
                'nullable',
                'int',
                'min:1',
                // 'exists:youths,id,deleted_at,NULL',
            ],
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
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'rto_batch_id' => 'nullable|int',
            'title_en' => 'nullable|min:2',
            'title' => 'nullable|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
        ], $customMessage);
    }
}
