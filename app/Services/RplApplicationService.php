<?php


namespace App\Services;


use App\Models\RplAssessment;
use App\Models\RplAssessmentQuestion;
use App\Models\BaseModel;
use App\Models\EducationLevel;
use App\Models\EnrollmentEducation;
use App\Models\RplApplication;
use App\Models\RplApplicationAddress;
use App\Models\RplApplicationEducation;
use App\Models\RplApplicationProfessionalQualification;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RplApplicationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @param bool $isPublicApi
     * @return array
     */
    public function getRplApplicationList(array $request, Carbon $startTime, bool $isPublicApi = false): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $order = $request['order'] ?? "ASC";
        $assessmentId = $request['assessment_id'] ?? "";
        $rplOccupationId = $request['rpl_occupation_id'] ?? "";
        $rplLevelId = $request['rpl_level_id'] ?? "";
        $rplSectorId = $request['rpl_sector_id'] ?? "";
        $rtoBatchId = $request['rto_batch_id'] ?? "";

        /** @var RplApplication|Builder $youthAssessmentBuilder */
        $youthAssessmentBuilder = RplApplication::select([
            'rpl_applications.id',
            'rpl_applications.youth_id',
            'rpl_applications.assessment_id',
            'rpl_applications.rto_batch_id',
            'rpl_applications.result',
            'rpl_applications.score',

            'rpl_applications.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_applications.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'rpl_applications.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'rpl_applications.rto_country_id',
            'rto_countries.title_en as rto_country_title_en',
            'rto_countries.title as rto_country_title',

            'rpl_applications.target_country_id',
            'target_countries.title_en as target_country_title_en',
            'target_countries.title as target_country_title',

            'rpl_applications.rto_id',
            'registered_training_organizations.title_en as rto_title_en',
            'registered_training_organizations.title as rto_title',

            'rpl_applications.created_at',
            'rpl_applications.updated_at',
            'rpl_applications.deleted_at',
        ]);

        if (!$isPublicApi) {
            $youthAssessmentBuilder->acl();
        }
        $youthAssessmentBuilder->orderBy('rpl_applications.id', $order);

        $youthAssessmentBuilder->join('rpl_occupations', function ($join) {
            $join->on('rpl_applications.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_levels', function ($join) {
            $join->on('rpl_applications.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_sectors', function ($join) {
            $join->on('rpl_applications.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as rto_countries', function ($join) {
            $join->on('rpl_applications.rto_country_id', '=', 'rto_countries.id')
                ->whereNull('rto_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as target_countries', function ($join) {
            $join->on('rpl_applications.target_country_id', '=', 'target_countries.id')
                ->whereNull('target_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('registered_training_organizations', function ($join) {
            $join->on('rpl_applications.rto_id', '=', 'registered_training_organizations.id')
                ->whereNull('registered_training_organizations.deleted_at');
        });

        if (!empty($titleEn)) {
            $youthAssessmentBuilder->where('rpl_applications.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $youthAssessmentBuilder->where('rpl_applications.title', 'like', '%' . $title . '%');
        }
        if (!empty($rplassessmentId)) {
            $youthAssessmentBuilder->where('rpl_applications.assessment_id', $assessmentId);
        }
        if (!empty($rplOccupationId)) {
            $youthAssessmentBuilder->where('rpl_applications.rpl_occupation_id', $rplOccupationId);
        }
        if (!empty($rplLevelId)) {
            $youthAssessmentBuilder->where('rpl_applications.rpl_level_id', $rplLevelId);
        }
        if (!empty($rplSectorId)) {
            $youthAssessmentBuilder->where('rpl_applications.rpl_sector_id', $rplSectorId);
        }
        if (!empty($rtoBatchId)) {
            $youthAssessmentBuilder->where('rpl_applications.rto_batch_id', $rtoBatchId);
        }
        $youthAssessmentBuilder->with('addresses');
        $youthAssessmentBuilder->with('educations');
        $youthAssessmentBuilder->with('professionalQualifications');


        /** @var Collection $youthAssessments */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $youthAssessments = $youthAssessmentBuilder->paginate($pageSize);
            $paginateData = (object)$youthAssessments->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $youthAssessments = $youthAssessmentBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $youthAssessments->toArray()['data'] ?? $youthAssessments->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];
        return $response;
    }

    /**
     * @param int $id
     * @return RplApplication
     */
    public function getOneRplApplication(int $id): RplApplication
    {
        /** @var RplApplication|Builder $youthAssessmentBuilder */
        $youthAssessmentBuilder = RplApplication::select([
            'rpl_applications.id',
            'rpl_applications.youth_id',
            'rpl_applications.assessment_id',
            'rpl_applications.rto_batch_id',
            'rpl_applications.result',
            'rpl_applications.score',

            'rpl_applications.rpl_occupation_id',
            'rpl_occupations.title_en as rpl_occupation_title_en',
            'rpl_occupations.title as rpl_occupation_title',

            'rpl_applications.rpl_level_id',
            'rpl_levels.title_en as rpl_level_title_en',
            'rpl_levels.title as rpl_level_title',

            'rpl_applications.rpl_sector_id',
            'rpl_sectors.title_en as rpl_sector_title_en',
            'rpl_sectors.title as rpl_sector_title',

            'rpl_applications.rto_country_id',
            'rto_countries.title_en as rto_country_title_en',
            'rto_countries.title as rto_country_title',

            'rpl_applications.target_country_id',
            'target_countries.title_en as target_country_title_en',
            'target_countries.title as target_country_title',

            'rpl_applications.rto_id',
            'registered_training_organizations.title_en as rto_title_en',
            'registered_training_organizations.title as rto_title',

            'rpl_applications.created_at',
            'rpl_applications.updated_at',
            'rpl_applications.deleted_at',
        ]);

        $youthAssessmentBuilder->where('rpl_applications.id', $id);

        $youthAssessmentBuilder->join('rpl_occupations', function ($join) {
            $join->on('rpl_applications.rpl_occupation_id', '=', 'rpl_occupations.id')
                ->whereNull('rpl_occupations.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_levels', function ($join) {
            $join->on('rpl_applications.rpl_level_id', '=', 'rpl_levels.id')
                ->whereNull('rpl_levels.deleted_at');
        });

        $youthAssessmentBuilder->join('rpl_sectors', function ($join) {
            $join->on('rpl_applications.rpl_sector_id', '=', 'rpl_sectors.id')
                ->whereNull('rpl_sectors.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as rto_countries', function ($join) {
            $join->on('rpl_applications.rto_country_id', '=', 'rto_countries.id')
                ->whereNull('rto_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('countries as target_countries', function ($join) {
            $join->on('rpl_applications.target_country_id', '=', 'target_countries.id')
                ->whereNull('target_countries.deleted_at');
        });

        $youthAssessmentBuilder->join('registered_training_organizations', function ($join) {
            $join->on('rpl_applications.rto_id', '=', 'registered_training_organizations.id')
                ->whereNull('registered_training_organizations.deleted_at');
        });
        $youthAssessmentBuilder->with('addresses');
        $youthAssessmentBuilder->with('educations');
        $youthAssessmentBuilder->with('professionalQualifications');

        return $youthAssessmentBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return RplApplication
     */
    public function storeRplAssessment(array $data): RplApplication
    {
        $rplApplication = app(RplApplication::class);
        $rplApplication->fill($data);
        $rplApplication->save();

        return $rplApplication;
    }

    /**
     * @param RplApplication $rplApplication
     * @param array $data
     * @return RplApplication
     */
    public function storeRplApplication(RplApplication $rplApplication, array $data): RplApplication
    {
        $rplApplication->fill($data);
        $rplApplication->save();

        return $rplApplication;

    }

    /**
     * @param array $data
     */
    public function storeRplApplicationAddress(array $data)
    {
        $rplApplicationId = $data['id'];

        RplApplicationAddress::where('rpl_application_id', $rplApplicationId)->delete();

        if (!empty($data['present_address'])) {
            $presentAddressData = $data['present_address'];
            $presentAddressData['rpl_application_id'] = $rplApplicationId;
            $presentAddressData['address_type'] = RplApplicationAddress::ADDRESS_TYPE_PRESENT;
            $presentAddress = app(RplApplicationAddress::class);


            $presentAddress->fill($presentAddressData);
            $presentAddress->save();
        }
        if (!empty($data['permanent_address'])) {
            $permanentAddress = app(RplApplicationAddress::class);
            $permanentAddressData = $data['permanent_address'];
            $permanentAddressData['rpl_application_id'] = $rplApplicationId;
            $permanentAddressData['address_type'] = RplApplicationAddress::ADDRESS_TYPE_PERMANENT;

            $permanentAddress->fill($permanentAddressData);
            $permanentAddress->save();
        }
    }

    /**
     * @param array $data
     */
    public function storeRplApplicationEducation(array $data)
    {
        $rplApplicationId = $data['id'];
        RplApplicationEducation::where('rpl_application_id', $rplApplicationId)->delete();


        if (!empty($data['education_info'])) {
            foreach ($data['education_info'] as $educationInfo) {
                $rplApplicationEducation = app(RplApplicationEducation::class);
                $educationInfo['rpl_application_id'] = $rplApplicationId;
                $rplApplicationEducation->fill($educationInfo);
                $rplApplicationEducation->save();
            }
        }
    }

    /**
     * @param array $data
     */
    public function storeRplApplicationProfessionalQualification(array $data)
    {
        $rplApplicationId = $data['id'];
        RplApplicationProfessionalQualification::where('rpl_application_id', $rplApplicationId)->delete();

        if (!empty($data['professional_qualifications'])) {
            foreach ($data['professional_qualifications'] as $professionalQualificationInfo) {
                $rplApplicationProfessionalQualification = app(RplApplicationProfessionalQualification::class);
                $professionalQualificationInfo['rpl_application_id'] = $rplApplicationId;
                $rplApplicationProfessionalQualification->fill($professionalQualificationInfo);
                $rplApplicationProfessionalQualification->save();
            }
        }
    }

    /**
     * @param RplApplication $youthAssessment
     * @param array $data
     * @return RplApplication
     */
    public function updateResult(RplApplication $youthAssessment, array $data): RplApplication
    {
        $correct = 0;
        $assessmentId = $data['assessment_id'];
        $answers = $data['answers'];
        $columns = [
            'rpl_assessment_questions.assessment_id',
            'rpl_assessment_questions.question_id',
            'rpl_assessment_questions.answer',
        ];
        $assessment = RplAssessment::select(['rpl_assessments.passing_score'])->where('id', $assessmentId)->first();
        $assessmentQs = RplAssessmentQuestion::select($columns)->where('assessment_id', $assessmentId)->get()->toArray();
        $questions = [];
        foreach ($assessmentQs as $ques) {
            $questions[$ques['question_id']] = $ques['answer'];
        }
        foreach ($answers as $ans) {
            $qid = $ans['question_id'];
            $answer = $ans['answer'];
            $correct += ($questions[$qid] == $answer) ? 1 : 0;
        }
        $score = ($correct / count($assessmentQs)) * 100;
        $update = [
            'result' => $score >= $assessment->passing_score ? 1 : 0,
            'score' => $score,
        ];
        $youthAssessment->fill($update);
        $youthAssessment->save();
        return $youthAssessment;
    }

    /**
     * @param RplApplication $youthAssessment
     * @param array $data
     * @return RplApplication
     */
    public function update(RplApplication $youthAssessment, array $data): RplApplication
    {
        $youthAssessment->fill($data);
        $youthAssessment->save();
        return $youthAssessment;
    }

    /**
     * @param RplApplication $youthAssessment
     * @return bool
     */
    public function destroy(RplApplication $youthAssessment): bool
    {
        return $youthAssessment->delete();
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
            'id' => [
                'nullable',
                'integer',
                Rule::exists('rpl_applications', 'id')
                    ->where(function ($query) use ($data) {
                        $query->where('rpl_applications.youth_id', $data['youth_id']);
                        $query->whereNull('rpl_applications.deleted_at');
                    })
            ],

            'registration_number' => [
                'nullable',
                'string',
            ],
            'first_name' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'string',
                'max:300'
            ],
            'last_name' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'string',
                'max:300'
            ],
            'first_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'last_name_en' => [
                'nullable',
                'string',
                'max:150'
            ],
            'father_name' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'string',
                'max:500'
            ],
            'father_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],

            'mother_name' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'string',
                'max:500'
            ],
            'mother_name_en' => [
                'nullable',
                'string',
                'max:250'
            ],
            "date_of_birth" => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'date',
                'date_format:Y-m-d',
                'before:today'
            ],
            "mobile" => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'max:11',
                BaseModel::MOBILE_REGEX,
            ],
            "nationality" => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'int'
            ],
            'identity_number_type' => [
                Rule::requiredIf(!empty($data['id'])),
                'int',
                Rule::in(RplApplication::IDENTITY_TYPES)
            ],
            'identity_number' => [
                Rule::requiredIf(!empty($data['id'])),
                'string',
                'nullable'
            ],
            'religion' => [
                Rule::requiredIf(!empty($data['id'])),
                'int',
                Rule::in(RplApplication::RELIGIONS)
            ],
            "photo" => [
                'nullable',
                'string',
                'max:600'
            ],
            'present_address' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'array',
            ],
            'present_address.loc_division_id' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'integer',
            ],
            'present_address.loc_district_id' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'integer',
            ],
            'present_address.loc_upazila_id' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'integer',
            ],
            'present_address.village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'present_address.village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'present_address.house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'present_address.house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'present_address.zip_or_postal_code' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],
            'permanent_address' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'array',
            ],
            'permanent_address.loc_division_id' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'integer',
            ],
            'permanent_address.loc_district_id' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'integer',
            ],
            'permanent_address.loc_upazila_id' => [
                Rule::requiredIf(!empty($data['id'])),
                'nullable',
                'integer',
            ],
            'permanent_address.village_or_area' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'permanent_address.village_or_area_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'permanent_address.house_n_road' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'permanent_address.house_n_road_en' => [
                'nullable',
                'string',
                'max:250',
                'min:2'
            ],
            'permanent_address.zip_or_postal_code' => [
                'nullable',
                'string',
                'max:5',
                'min:4'
            ],
            'guardian_name' => [
                'nullable',
                'string',
                'max:500',
                'min:2'
            ],
            'guardian_name_en' => [
                'nullable',
                'string',
                'max:300',
                'min:2'
            ],
            'is_currently_working' => [
                Rule::requiredIf(!empty($data['id'])),
                'integer',
                Rule::in(RplApplication::IS_YOUTH_CURRENTLY_WORKING)
            ],
            'company_type' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['is_currently_working']) && $data['is_currently_working'] == RplApplication::IS_YOUTH_CURRENTLY_WORKING_TRUE;
                }),
                'nullable',
                'string'
            ],
            'position' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['is_currently_working']) && $data['is_currently_working'] == RplApplication::IS_YOUTH_CURRENTLY_WORKING_TRUE;
                }),
                'nullable',
                'string'
            ],
            'position_en' => [
                'string',
                'nullable'
            ],
            'company_name' => [
                Rule::requiredIf(function () use ($data) {
                    return !empty($data['is_currently_working']) && $data['is_currently_working'] == RplApplication::IS_YOUTH_CURRENTLY_WORKING;
                }),
                'nullable',
                'string',
            ],
            'company_name_en' => [
                'string',
                'nullable'
            ],

            'professional_qualifications' => [
                'nullable',
                'array'
            ],
            'professional_qualifications.*' => [
                'nullable',
                'array'
            ],
            'professional_qualifications.*.rto_country_id' => [
                Rule::requiredIf(!empty($data['professional_qualifications'])),
                'nullable',
                'int',
                'min:1',
                'exists:rto_countries,country_id',
            ],
            'professional_qualifications.*.rpl_sector_id' => [
                Rule::requiredIf(!empty($data['professional_qualifications'])),
                'nullable',
                'int',
                'min:1',
                'exists:rpl_sectors,id,deleted_at,NULL',
            ],
            'professional_qualifications.*.rpl_occupation_id' => [
                Rule::requiredIf(!empty($data['professional_qualifications'])),
                'nullable',
                'int',
                'min:1',
                'exists:rpl_occupations,id,deleted_at,NULL',
            ],
            'professional_qualifications.*.rpl_level_id' => [
                Rule::requiredIf(!empty($data['professional_qualifications'])),
                'nullable',
                'int',
                'min:1',
                'exists:rpl_levels,id,deleted_at,NULL',
            ],
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
            'rpl_level_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_levels,id,deleted_at,NULL',
            ],
            'youth_id' => [
                'required',
                'int',
                'min:1',
            ],
            'assessment_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_assessments,id,deleted_at,NULL',
            ],
            'target_country_id' => [
                'required',
                'int',
                'min:1',
                'exists:rto_countries,country_id',
            ],
            'rto_country_id' => [
                'required',
                'int',
                'min:1',
                'exists:rto_countries,country_id',
            ],
            'rto_id' => [
                'required',
                'int',
                'min:1',
                'exists:registered_training_organizations,id,deleted_at,NULL',
            ],
            'rto_batch_id' => [
                'nullable',
                'int',
                'min:1',
                'exists:rto_batches,id,deleted_at,NULL',
            ],
            'education_info' => [
                Rule::requiredIf(!empty($data['id'])),
                'min:1',
                'array',
            ],
            'education_info.*' => [
                Rule::requiredIf(!empty($data['id'])),
                'min:1',
                'array',
            ],
        ];

        if (!empty($data['education_info'])) {
            $index = 0;
            foreach ($data['education_info'] as $educationInfo) {
                $rules['education_info.' . $index . '.education_level_id'] = [
                    'required',
                    'min:1',
                    'integer',
                    'exists:education_levels,id,deleted_at,NULL',
                ];
                $eduLabelId = $educationInfo['education_level_id'] ?? 0;
                $rules['education_info.' . $index . '.exam_degree_name'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::EXAM_DEGREE_NAME, $eduLabelId);
                    }),
                    'nullable',
                    "string"
                ];
                $rules['education_info.' . $index . '.exam_degree_name_en'] = [
                    "nullable",
                    "string"
                ];
                $rules['education_info.' . $index . '.major_or_concentration'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::MAJOR, $eduLabelId);
                    }),
                    'nullable',
                    "string"
                ];
                $rules['education_info.' . $index . '.major_or_concentration_en'] = [
                    "nullable",
                    "string"
                ];
                $rules['education_info.' . $index . '.edu_group_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::EDU_GROUP, $eduLabelId);
                    }),
                    'nullable',
                    'exists:edu_groups,id,deleted_at,NULL',
                    "integer"
                ];
                $rules['education_info.' . $index . '.edu_board_id'] = [
                    Rule::requiredIf(function () use ($eduLabelId, $data) {
                        return $this->getRequiredStatus(EnrollmentEducation::BOARD, $eduLabelId);
                    }),
                    'nullable',
                    'exists:edu_boards,id,deleted_at,NULL',
                    "integer"
                ];
                $rules['education_info.' . $index . '.institute_name'] = [
                    Rule::requiredIf(!empty($data['education_info'])),
                    'string',
                    'max:800',
                ];
                $rules['education_info.' . $index . '.institute_name_en'] = [
                    'nullable',
                    'string',
                    'max:400',
                ];
                $rules['education_info.' . $index . '.result'] = [
                    Rule::requiredIf(!empty($data['education_info'])),
                    "integer",
                    Rule::in(array_keys(config("nise3.exam_degree_results")))
                ];
                $rules['education_info.' . $index . '.marks_in_percentage'] = [
                    Rule::requiredIf(function () use ($educationInfo) {
                        $resultId = !empty($educationInfo['result']) ? $educationInfo['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::MARKS, $resultId);
                    }),
                    'nullable',
                    "numeric"
                ];
                $rules['education_info.' . $index . '.cgpa_scale'] = [
                    Rule::requiredIf(function () use ($educationInfo) {
                        $resultId = !empty($educationInfo['result']) ? $educationInfo['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::SCALE, $resultId);
                    }),
                    'nullable',
                    "integer",
                    Rule::in([EnrollmentEducation::GPA_OUT_OF_FOUR, EnrollmentEducation::GPA_OUT_OF_FIVE]),
                ];
                $rules['education_info.' . $index . '.cgpa'] = [
                    Rule::requiredIf(function () use ($educationInfo) {
                        $resultId = !empty($educationInfo['result']) ? $educationInfo['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::CGPA, $resultId);
                    }),
                    'nullable',
                    'numeric',
                    'max:5'
                ];
                $rules['education_info.' . $index . '.year_of_passing'] = [
                    Rule::requiredIf(function () use ($educationInfo) {
                        $resultId = !empty($educationInfo['result']) ? $educationInfo['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::YEAR_OF_PASS, $resultId);
                    }),
                    'nullable',
                    'string'
                ];
                $rules['education_info.' . $index . '.expected_year_of_passing'] = [
                    Rule::requiredIf(function () use ($educationInfo) {
                        $resultId = !empty($educationInfo['result']) ? $educationInfo['result'] : null;
                        return $resultId && $this->getRequiredStatus(EnrollmentEducation::EXPECTED_YEAR_OF_PASS, $resultId);
                    }),
                    'nullable',
                    'string'
                ];
                $rules['education_info.' . $index . '.duration'] = [
                    "nullable",
                    "integer"
                ];
                $index++;
            }

        }
        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }

    /**
     * @param string $modelName
     * @param int $id
     * @return string
     */
    public function getCodeById(string $modelName, int $id): string
    {
        if ($modelName == EnrollmentEducation::EDUCATION_LEVEL_TRIGGER) {
            $educationLevel = EducationLevel::where('id', $id)->first();
            $code = $educationLevel->code ?? "";
        } else {
            $code = config("nise3.exam_degree_results." . $id . ".code");
        }
        return $code ?? "";
    }

    /**
     * @param string $key
     * @param int $eduLabelId
     * @return bool
     */
    private function getRequiredStatus(string $key, int $eduLabelId): bool
    {
        switch ($key) {

            /** Validation Rule Based On YouthEducation Level */
            case EnrollmentEducation::DEGREE:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_PSC_5_PASS, EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS, EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY, EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS]);
            }
            case EnrollmentEducation::BOARD:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_PSC_5_PASS, EducationLevel::EDUCATION_LEVEL_JSC_JDC_8_PASS, EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY]);
            }
            case EnrollmentEducation::MAJOR:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_DIPLOMA, EducationLevel::EDUCATION_LEVEL_BACHELOR, EducationLevel::EDUCATION_LEVEL_MASTERS, EducationLevel::EDUCATION_LEVEL_PHD]);
            }
            case EnrollmentEducation::EXAM_DEGREE_NAME:
            {
                return $this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId) == EducationLevel::EDUCATION_LEVEL_PHD;
            }
            case EnrollmentEducation::EDU_GROUP:
            {
                return in_array($this->getCodeById(EnrollmentEducation::EDUCATION_LEVEL_TRIGGER, $eduLabelId), [EducationLevel::EDUCATION_LEVEL_SECONDARY, EducationLevel::EDUCATION_LEVEL_HIGHER_SECONDARY]);
            }
            /** Validation Rule Based On Result Type */
            case EnrollmentEducation::MARKS:
            {
                return in_array($this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId), [EducationLevel::RESULT_FIRST_DIVISION, EducationLevel::RESULT_SECOND_DIVISION, EducationLevel::RESULT_THIRD_DIVISION]);
            }
            case EnrollmentEducation::SCALE:
            case EnrollmentEducation::CGPA:
            {
                return $this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId) == EducationLevel::RESULT_GRADE;
            }
            case EnrollmentEducation::YEAR_OF_PASS:
            {
                return $this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId) !== EducationLevel::RESULT_APPEARED;
            }
            case EnrollmentEducation::EXPECTED_YEAR_OF_PASS:
            {
                return $this->getCodeById(EnrollmentEducation::RESULT_TRIGGER, $eduLabelId) == EducationLevel::RESULT_APPEARED;
            }
            default:
            {
                return false;
            }

        }
    }

    /**
     * @param Request $request
     * @param int|null $id
     * @return Validator
     */
    public function answersValidator(Request $request, int $id = null): Validator
    {
        $data = $request->all();

        $rules = [
            'assessment_id' => [
                'required',
                'int',
                'min:1',
                'exists:rpl_assessments,id,deleted_at,NULL',
            ],
            'answers' => [
                'required',
                'array',
                'min:1'
            ],
            'answers .*' => [
                Rule::requiredIf(!empty($data['answers'])),
                'array',
                'min:1'
            ],
            'answers .*.question_id' => [
                Rule::requiredIf(!empty($data['answers'])),
                'int',
                'min:1'
            ],
            'answers .*.answer' => [
                Rule::requiredIf(!empty($data['answers'])),
                'int',
                Rule::in([1, 2, 3, 4])
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
            'order . in' => 'Order must be either ASC or DESC . [30000]',
        ];

        return \Illuminate\Support\Facades\Validator::make($request->all(), [
            'assessment_id' => 'nullable | int',
            'rpl_occupation_id' => 'nullable | int',
            'rpl_level_id' => 'nullable | int',
            'rpl_sector_id' => 'nullable | int',
            'rto_batch_id' => 'nullable | int',
            'title_en' => 'nullable | min:2',
            'title' => 'nullable | min:2',
            'page_size' => 'int | gt:0',
            'page' => 'integer | gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
        ], $customMessage);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Validator
     */
    public function assignToBatchValidator(Request $request, int $id): Validator
    {
        $data = $request->all();

        $rules = [
            'rto_batch_id' => [
                'required',
                'int',
                'min:1',
                'exists:rto_batches,id,deleted_at,NULL',
            ],
        ];

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }
}
