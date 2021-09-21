<?php

namespace App\Services;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use App\Models\Programme;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProgrammeService
 * @package App\Services
 */
class ProgrammeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getProgrammeList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $titleBn = $request['title_bn'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Programme|Builder $programmesBuilder */
        $programmesBuilder = Programme::select([
            'programmes.id',
            'programmes.title_en',
            'programmes.title_bn',
            'programmes.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'programmes.code',
            'programmes.logo',
            'programmes.description',
            'programmes.row_status',
            'programmes.created_by',
            'programmes.updated_by',
            'programmes.created_at',
            'programmes.updated_at',
            'programmes.deleted_at',
        ]);

        $programmesBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('programmes.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $programmesBuilder->orderBy('programmes.id', $order);

        if (is_numeric($rowStatus)) {
            $programmesBuilder->where('programmes.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $programmesBuilder->where('programmes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $programmesBuilder->where('programmes.title_bn', 'like', '%' . $titleBn . '%');
        }
        if ($instituteId) {
            $programmesBuilder->where('programmes.institute_id', '=', $instituteId);
        }


        /** @var Collection $programmesBuilder */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $programmes = $programmesBuilder->paginate($pageSize);
            $paginateData = (object)$programmes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $programmes = $programmesBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $programmes->toArray()['data'] ?? $programmes->toArray();
        $response['_response_status'] = [
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
    public function getOneProgramme(int $id, Carbon $startTime): array
    {
        /** @var Programme|Builder $programmeBuilder */
        $programmeBuilder = Programme::select([
            'programmes.id',
            'programmes.title_en',
            'programmes.title_bn',
            'programmes.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title_bn as institute_title_bn',
            'programmes.code',
            'programmes.logo',
            'programmes.description',
            'programmes.row_status',
            'programmes.created_by',
            'programmes.updated_by',
            'programmes.created_at',
            'programmes.updated_at',
            'programmes.deleted_at',
        ]);
        $programmeBuilder->join("institutes", function ($join) {
            $join->on('programmes.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $programmeBuilder->where('programmes.id', '=', $id);

        /** @var Programme $programmeBuilder */
        $programme = $programmeBuilder->first();
        $links = [];
        return [
            "data" => $programme ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffInSeconds(Carbon::now()),
            ]
        ];
    }

    /**
     * @param array $data
     * @return Programme
     */
    public function store(array $data): Programme
    {
        $programme = new Programme();
        $programme->fill($data);
        $programme->Save();
        return $programme;
    }

    /**
     * @param Programme $programme
     * @param array $data
     * @return Programme
     */
    public function update(Programme $programme, array $data): Programme
    {
        $programme->fill($data);
        $programme->save();
        return $programme;
    }

    /**
     * @param Programme $programme
     * @return bool
     */
    public function destroy(Programme $programme): bool
    {
        return $programme->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        $rules = [
            'title_en' => [
                'required',
                'string',
                'max:300',
                'min:2'
            ],
            'title_bn' => [
                'required',
                'string',
                'max:800',
                'min:2'
            ],
            'institute_id' => [
                'required',
                'int',
                'exists:institutes,id'
            ],
            'code' => [
                'nullable',
                'string',
                'max:191',
                'unique:programmes,code,' . $id,
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'logo' => [
                'nullable',
                'string'
            ],
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'created_by' => ['nullable', 'integer', 'max:10'],
            'updated_by' => ['nullable', 'integer', 'max:10'],
        ];
        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $customMessage);
    }


    public function getProgrammeTrashList(Request $request, Carbon $startTime): array
    {
        $limit = $request->query('limit', 10);
        $titleEn = $request->query('title_en');
        $titleBn = $request->query('title_bn');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Programme|Builder $programmesBuilder */
        $programmesBuilder = Programme::onlyTrashed()->select([
            'programmes.id as id',
            'programmes.title_en',
            'programmes.title_bn',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'programmes.code as programme_code',
            'programmes.logo as programme_logo',
            'programmes.description',
            'programmes.row_status',
            'programmes.created_at',
            'programmes.updated_at',
        ]);
        $programmesBuilder->join('institutes', 'programmes.institute_id', '=', 'institutes.id');
        $programmesBuilder->orderBy('programmes.id', $order);

        if (!empty($titleEn)) {
            $programmesBuilder->where('programmes.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $programmesBuilder->where('programmes.title_bn', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $programmesBuilder */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $programmes = $programmesBuilder->paginate($limit);
            $paginateData = (object)$programmes->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $programmes = $programmesBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $programmes->toArray()['data'] ?? $programmes->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }


    public function restore(Programme $programmes): bool
    {
        return $programmes->restore();
    }

    public function forceDelete(Programme $programmes): bool
    {
        return $programmes->forceDelete();
    }

    public function filterValidator(Request $request): Validator
    {
        if (!empty($request['order'])) {
            $request['order'] = strtoupper($request['order']);
        }
        $customMessage = [
            'order.in' => [
                'code' => 30000,
                "message" => 'Order must be within ASC or DESC',
            ],
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|string|max:300|min:2',
            'title_bn' => 'nullable|string|max:800|min:2',
            'page_size' => 'numeric|gt:0',
            'page' => 'numeric|gt:0',
            'institute_id' => 'numeric|exists:institutes,id',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

}
