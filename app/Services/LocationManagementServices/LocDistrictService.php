<?php

namespace App\Services\LocationManagementServices;

use App\Models\BaseModel;
use App\Models\LocDistrict;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class LocDistrictService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getAllDistricts(array $request, Carbon $startTime): array
    {
        $titleEn = array_key_exists('title_en', $request) ? $request['title_en'] : "";
        $titleBn = array_key_exists('title_bn', $request) ? $request['title_bn'] : "";
        $rowStatus = array_key_exists('row_status', $request) ? $request['row_status'] : "";
        $divisionId = array_key_exists('division_id', $request) ? $request['division_id'] : "";
        $order = array_key_exists('order', $request) ? $request['order'] : "ASC";

        /** @var Builder $districtsBuilder */
        $districtsBuilder = LocDistrict::select([
            'loc_districts.id',
            'loc_districts.loc_division_id',
            'loc_districts.title_bn',
            'loc_districts.title_en',
            'loc_districts.bbs_code',
            'loc_districts.division_bbs_code',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'loc_districts.row_status',
            'loc_districts.created_by',
            'loc_districts.updated_by',
            'loc_districts.created_at',
            'loc_districts.updated_at'

        ]);

        $districtsBuilder->leftJoin('loc_divisions', function ($join) use ($rowStatus) {
            $join->on('loc_divisions.id', '=', 'loc_districts.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
            if (is_numeric($rowStatus)) {
                $join->where('loc_divisions.row_status', $rowStatus);
            }
        });

        $districtsBuilder->orderBy('loc_districts.id', $order);

        if (is_numeric($rowStatus)) {
            $districtsBuilder->where('loc_districts.row_status', $rowStatus);
            $response['row_status'] = $rowStatus;
        }

        if (!empty($titleEn)) {
            $districtsBuilder->where('loc_districts.title_en', 'like', '%' . $titleEn . '%');
        } elseif (!empty($titleBn)) {
            $districtsBuilder->where('loc_districts.title_bn', 'like', '%' . $titleBn . '%');
        }

        if (!empty($divisionId)) {
            $districtsBuilder->where('loc_districts.loc_division_id', $divisionId);
        }

        $districtsBuilder = $districtsBuilder->get();

        $response['order'] = $order;
        $response['data'] = $districtsBuilder->toArray()['data'] ?? $districtsBuilder->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffForHumans(Carbon::now())
        ];

        return $response;
    }

    /**
     * @param int $id
     * @param Carbon $startTime
     * @return array
     */
    public function getOneDistrict(int $id, Carbon $startTime): array
    {
        /** @var Builder $districtBuilder */
        $districtBuilder = LocDistrict::select([
            'loc_districts.id',
            'loc_districts.loc_division_id',
            'loc_districts.title_bn',
            'loc_districts.title_en',
            'loc_districts.bbs_code',
            'loc_districts.division_bbs_code',
            'loc_divisions.title_bn as division_title_bn',
            'loc_divisions.title_en as division_title_en',
            'loc_districts.row_status',
            'loc_districts.created_by',
            'loc_districts.updated_by',
            'loc_districts.created_at',
            'loc_districts.updated_at'
        ]);

        $districtBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_districts.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $districtBuilder->where('loc_districts.id', $id);
        $district = $districtBuilder->first();
        return [
            "data" => $district ?: [],
            "_response_status" => [
                "success" => true,
                "code" => Response::HTTP_OK,
                "query_time" => $startTime->diffForHumans(Carbon::now())
            ]
        ];
    }

    /**
     * @param array $data
     * @return LocDistrict
     */
    public function store(array $data): LocDistrict
    {
        $locDistrict = new LocDistrict();
        $locDistrict->fill($data);
        $locDistrict->save();
        return $locDistrict;
    }

    /**
     * @param LocDistrict $locDistrict
     * @param array $data
     * @return LocDistrict
     */
    public function update(LocDistrict $locDistrict, array $data): LocDistrict
    {
        $locDistrict->fill($data);
        $locDistrict->save();
        return $locDistrict;
    }

    /**
     * @param LocDistrict $locDistrict
     * @return bool
     */
    public function destroy(LocDistrict $locDistrict): bool
    {
        return $locDistrict->delete();
    }

    public function validator(Request $request, int $id = null): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'row_status.in' => [
                'code' => 30000,
                'message' => 'Row status must be within 1 or 0'
            ]
        ];

        return Validator::make($request->all(), [
            'loc_division_id' => 'required|numeric|exists:loc_divisions,id',
            'title_en' => 'required|min:2',
            'title_bn' => 'required|min:2',
            'bbs_code' => 'nullable|min:1',
            'division_bbs_code' => 'nullable|min:1|exists:loc_divisions,bbs_code',
            'row_status' => [
                'required_if:' . $id . ',!=,null',
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }

    public function filterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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

        return Validator::make($request->all(), [
            'title_en' => 'nullable|min:1',
            'title_bn' => 'nullable|min:1',
            'division_id' => 'numeric',
            'order' => [
                'string',
                Rule::in([(BaseModel::ROW_ORDER_ASC), (BaseModel::ROW_ORDER_DESC)])
            ],
            'row_status' => [
                "numeric",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
        ], $customMessage);
    }
}
