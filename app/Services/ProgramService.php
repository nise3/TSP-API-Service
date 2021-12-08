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

        /** @var Program|Builder $programsBuilder */
        $programsBuilder = Program::select([
            'programs.id',
            'programs.title_en',
            'programs.title',
            'programs.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'programs.code',
            'programs.logo',
            'programs.description',
            'programs.description_en',
            'programs.row_status',
            'programs.created_by',
            'programs.updated_by',
            'programs.created_at',
            'programs.updated_at',
            'programs.deleted_at',
        ])->acl();

        $programsBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('programs.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $programsBuilder->orderBy('programs.id', $order);

        if (is_numeric($rowStatus)) {
            $programsBuilder->where('programs.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $programsBuilder->where('programs.title_en', 'like', '%' . $titleEn . '%');
        }

        if (!empty($title)) {
            $programsBuilder->where('programs.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($instituteId)) {
            $programsBuilder->where('programs.institute_id', '=', $instituteId);
        }


        /** @var Collection $programs */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $programs = $programsBuilder->paginate($pageSize);
            $paginateData = (object)$programs->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $programs = $programsBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $programs->toArray()['data'] ?? $programs->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }


    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getPublicProgramList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Program|Builder $programsBuilder */
        $programsBuilder = Program::select([
            'programs.id',
            'programs.title_en',
            'programs.title',
            'programs.institute_id',
            'institutes.title_en as institute_title_en',
            'institutes.title as institute_title',
            'programs.code',
            'programs.logo',
            'programs.description_en',
            'programs.description',
            'programs.row_status',
            'programs.created_by',
            'programs.updated_by',
            'programs.created_at',
            'programs.updated_at',
            'programs.deleted_at',
        ]);

        $programsBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('programs.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
        });

        $programsBuilder->orderBy('programs.id', $order);

        if (is_numeric($rowStatus)) {
            $programsBuilder->where('programs.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $programsBuilder->where('programs.title_en', 'like', '%' . $titleEn . '%');
        }

        if (!empty($title)) {
            $programsBuilder->where('programs.title', 'like', '%' . $title . '%');
        }

        if (is_numeric($instituteId)) {
            $programsBuilder->where('programs.institute_id', '=', $instituteId);
        }


        /** @var Collection $programs */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $programs = $programsBuilder->paginate($pageSize);
            $paginateData = (object)$programs->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $programs = $programsBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $programs->toArray()['data'] ?? $programs->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return Program
     */
    public function getOneProgramme(int $id): Program
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
            'programs.description_en',
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
        return $programmeBuilder->firstOrFail();
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
            'row_status.in' => 'Order must be either ASC or DESC. [30000]',
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
                'exists:institutes,id,deleted_at,NULL',
                'int',
            ],
            'code' => [
                'nullable',
                'unique:programs,code,' . $id,
                'string',
                'max:100',
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
                'nullable',
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

        /** @var Program|Builder $programsBuilder */
        $programsBuilder = Program::onlyTrashed()->select([
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
        $programsBuilder->join('institutes', 'programs.institute_id', '=', 'institutes.id');
        $programsBuilder->orderBy('programs.id', $order);

        if (!empty($titleEn)) {
            $programsBuilder->where('programs.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $programsBuilder->where('programs.title', 'like', '%' . $titleBn . '%');
        }

        /** @var Collection $programsBuilder */
        if ($paginate || $limit) {
            $limit = $limit ?: 10;
            $programs = $programsBuilder->paginate($limit);
            $paginateData = (object)$programs->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $programs = $programsBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $programs->toArray()['data'] ?? $programs->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }


    public function restore(Program $programs): bool
    {
        return $programs->restore();
    }

    public function forceDelete(Program $programs): bool
    {
        return $programs->forceDelete();
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
            'row_status.in' => 'Row status must be either 1 or 0. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title_en' => 'nullable|max:500|min:2',
            'title' => 'nullable|max:1000|min:2',
            'page_size' => 'int|gt:0',
            'page' => 'int|gt:0',
            'institute_id' => 'exists:institutes,id,deleted_at,NULL|integer',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "int",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getProgramTitle(Request $request): array
    {
        /** @var Program|Builder $programBuilder */
        $programBuilder = Program::select([
            'id',
            'title',
            'title_en'
        ]);

        if($request->filled('program_ids') && is_array($request->input('program_ids'))){
            $programBuilder->whereIn("id", $request->input('program_ids'));
        }

        return $programBuilder->get()->keyBy("id")->toArray();
    }

}
