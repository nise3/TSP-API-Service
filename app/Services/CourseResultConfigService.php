<?php

namespace App\Services;

use App\Models\BaseModel;
use App\Models\CourseResultConfig;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CourseResultConfigService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getList(array $request, Carbon $startTime): array
    {
        $courseId = $request['course_id'] ?? "";

        /** @var CourseResultConfig|Builder $courseResultConfigBuilder */
        $courseResultConfigBuilder = CourseResultConfig::select([
            'course_result_configs.id',
            'course_result_configs.institute_id',
            'course_result_configs.industry_association_id',
            'course_result_configs.course_id',
            'course_result_configs.result_type',
            'course_result_configs.gradings',
            'course_result_configs.result_percentages',
            'course_result_configs.pass_marks',
            'course_result_configs.total_attendance_marks',
            'course_result_configs.created_at',
            'course_result_configs.updated_at',
            'course_result_configs.deleted_at',
        ]);

        if (is_numeric($courseId)) {
            $courseResultConfigBuilder->where('course_result_configs.course_id', $courseId);
        }

        $courseResultConfig = $courseResultConfigBuilder->first();

        $response['order'] = 'ASC';
        $response['data'] = $courseResultConfig;
        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return CourseResultConfig
     */
    public function getOneCourseResultConfig(int $id): CourseResultConfig
    {
        /** @var CourseResultConfig|Builder $courseResultConfigBuilder */
        $courseResultConfigBuilder = CourseResultConfig::select([
            'course_result_configs.id',
            'course_result_configs.institute_id',
            'course_result_configs.industry_association_id',
            'course_result_configs.course_id',
            'course_result_configs.result_type',
            'course_result_configs.gradings',
            'course_result_configs.result_percentages',
            'course_result_configs.pass_marks',
            'course_result_configs.total_attendance_marks',
            'course_result_configs.created_at',
            'course_result_configs.updated_at',
            'course_result_configs.deleted_at',
        ]);
        $courseResultConfigBuilder->where('course_result_configs.id', $id);

        /** @var CourseResultConfig $courseResultConfig */
        return $courseResultConfigBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return CourseResultConfig
     */
    public function store(array $data): CourseResultConfig
    {
        return CourseResultConfig::updateOrCreate(
            ['course_id' => $data['course_id']],
            $data
        );
    }

    /**
     * @param CourseResultConfig $courseResultConfig
     * @return bool
     */
    public function destroy(CourseResultConfig $courseResultConfig): bool
    {
        return $courseResultConfig->delete();
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function validator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $customMessage = [
            'row_status.in' => 'Order must be either ASC or DESC. [30000]',
        ];
        $authUser = Auth::user();
        $rules = [
            'course_id' => [
                'required',
                'int'
            ],
            'institute_id' => [
                Rule::requiredIf(function () use ($authUser, $request) {
                    if ($authUser && $authUser->user_type == BaseModel::INSTITUTE_USER_TYPE) {
                        return true;
                    } elseif ($authUser && $authUser->user_type == BaseModel::SYSTEM_USER_TYPE && empty($request->get('industry_association_id'))) {
                        return true;
                    }
                    return false;
                }),
                "nullable",
                "exists:institutes,id,deleted_at,NULL",
                "int"
            ],
            'industry_association_id' => [
                Rule::requiredIf(function () use ($authUser, $request) {
                    if ($authUser && $authUser->user_type == BaseModel::INDUSTRY_ASSOCIATION_USER_TYPE) {
                        return true;
                    } elseif ($authUser && $authUser->user_type == BaseModel::SYSTEM_USER_TYPE && empty($request->get('institute_id'))) {
                        return true;
                    }
                    return false;
                }),
                "nullable",
                "int"
            ],
            'result_type' => [
                'required',
                'int',
                Rule::in(BaseModel::RESULT_TYPES)
            ],
            'gradings' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['result_type']) && $data['result_type'] == BaseModel::RESULT_TYPE_GRADING;
                }),
                'nullable',
                'array',
                'min:1',
                function ($attr, $value, $failed) use ($data) {
                    if(!empty($data['gradings'])){
                        if ($data['gradings'][0]['min'] !== '0') {
                            $failed("initial value should start from 0!");
                        }
                        $maxValue = null;

                        foreach ($data['gradings'] as $grading){
                            if($grading['min'] >= $grading['max']){
                                $failed("max value should be greater than min");
                            }
                            if($grading['min'] > $maxValue){
                                $maxValue = $grading['max'];
                            }else{
                                $failed("range should be greater than previous");
                            }

                        }
                    }
                }
            ],
            'gradings.*.min' => [
                'required',
                'int',
                'min:0',
                'max:100',
            ],
            'gradings.*.max' => [
                'required',
                'int',
                'min:1',
                'max:100',
            ],
            'gradings.*.label' => [
                'required',
                'string'
            ],
            'pass_marks' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['result_type']) && $data['result_type'] == BaseModel::RESULT_TYPE_MARKS;
                }),
                'max:100',
                "nullable",
                "numeric"
            ],
            "result_percentages" => [
                'required',
                'min:1',
                'array',
                function ($attr, $value, $failed) use ($data) {
                    $maxValue = null;
                    $totalPercentage = 0;
                    foreach ($data['result_percentages'] as $percentage){
                        $totalPercentage += $percentage;
                    }

                    if($totalPercentage > 100){
                        $failed("total percentage should not be greater than 100");
                    }
                }
            ],
            "result_percentages.online" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.offline" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.mixed" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.practical" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.field_work" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.presentation" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.assignment" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "result_percentages.attendance" => [
                'nullable',
                'min:1',
                'max:100',
                'int'
            ],
            "total_attendance_marks" => [
                Rule::requiredIf(!empty($data['result_percentages']['attendance'])),
                "nullable",
                "numeric"
            ]
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules, $customMessage);
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

        $rules = [

            'course_id' => 'required|int|gt:0'
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
    }
}

