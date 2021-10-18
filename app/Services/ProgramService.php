<?php

namespace App\Services;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProgramService
 * @package App\Services
 */
class ProgramService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getProgrammeList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Program|Builder $programmesBuilder */
        $programmesBuilder = Program::select([
            'programs.id',
            'programs.title_en',
            'programs.title',
            'programs.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'programs.code',
            'programs.logo',
            'programs.description',
            'programs.row_status',
            'programs.created_by',
            'programs.updated_by',
            'programs.created_at',
            'programs.updated_at',
            'programs.deleted_at',
        ]);

        $programmesBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('programs.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_int($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $programmesBuilder->orderBy('programs.id', $order);

        if (is_int($rowStatus)) {
            $programmesBuilder->where('programs.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $programmesBuilder->where('programs.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $programmesBuilder->where('programs.title', 'like', '%' . $title . '%');
        }
        if (is_int($instituteId)) {
            $programmesBuilder->where('programs.institute_id', '=', $instituteId);
        }


        /** @var Collection $programmes */
        if (is_int($paginate) || is_int($pageSize)) {
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
        /** @var Program|Builder $programmeBuilder */
        $programmeBuilder = Program::select([
            'programs.id',
            'programs.title_en',
            'programs.title',
            'programs.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'programs.code',
            'programs.logo',
            'programs.description',
            'programs.row_status',
            'programs.created_by',
            'programs.updated_by',
            'programs.created_at',
            'programs.updated_at',
            'programs.deleted_at',
        ]);
        $programmeBuilder->join("institutes", function ($join) {
            $join->on('programs.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $programmeBuilder->where('programs.id', '=', $id);

        /** @var Program $programme */
        $programme = $programmeBuilder->first();

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
     * @return Program
     */
    public function store(array $data): Program
    {
        $programme = new Program();
        $programme->fill($data);
        $programme->Save();
        return $programme;
    }

    /**
     * @param Program $programme
     * @param array $data
     * @return Program
     */
    public function update(Program $programme, array $data): Program
    {
        $programme->fill($data);
        $programme->save();
        return $programme;
    }

    /**
     * @param Program $programme
     * @return bool
     */
    public function destroy(Program $programme): bool
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
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'title' => [
                'required',
                'string',
                'max:1000',
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
                'max:100',
                'unique:programs,code,' . $id,
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'description_en' => [
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
        $titleBn = $request->query('title');
        $paginate = $request->query('page');
        $order = !empty($request->query('order')) ? $request->query('order') : 'ASC';

        /** @var Program|Builder $programmesBuilder */
        $programmesBuilder = Program::onlyTrashed()->select([
            'programs.id as id',
            'programs.title_en',
            'programs.title',
            'institutes.title_en as institute_title_en',
            'institutes.id as institute_id',
            'programs.code as program_code',
            'programs.logo as program_logo',
            'programs.description',
            'programs.row_status',
            'programs.created_at',
            'programs.updated_at',
        ]);
        $programmesBuilder->join('institutes', 'programs.institute_id', '=', 'institutes.id');
        $programmesBuilder->orderBy('programs.id', $order);

        if (!empty($titleEn)) {
            $programmesBuilder->where('programs.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $programmesBuilder->where('programs.title', 'like', '%' . $titleBn . '%');
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


    public function restore(Program $programmes): bool
    {
        return $programmes->restore();
    }

    public function forceDelete(Program $programmes): bool
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
            'title_en' => 'nullable|max:500|min:2',
            'title' => 'nullable|max:1000|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'institute_id' => 'integer|exists:institutes,id',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

}
