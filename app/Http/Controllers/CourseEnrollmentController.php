<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\CourseEnrollmentService;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CourseEnrollmentController extends Controller
{

    /**
     * @var CourseEnrollmentService
     */
    public CourseEnrollmentService $courseEnrollService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * CourseEnrollmentController constructor.
     * @param CourseEnrollmentService $courseEnrollService
     */
    public function __construct(CourseEnrollmentService $courseEnrollService)
    {
        $this->courseEnrollService = $courseEnrollService;
        $this->startTime = Carbon::now();
    }

    public function getCourseEnrollmentList(array $request, Carbon $startTime): array
    {
        $firstName = $request['first_name'] ?? "";
        $firstNameEn = $request['first_name_en'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $courseId = $request['course_id'] ?? "";
        $trainingCenterId = $request['training_center_id'] ?? "";
        $programId = $request['program_id'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";

        /** @var Course|Builder $coursesEnrollmentBuilder */
        $coursesEnrollmentBuilder = CourseEnrollment::select(
            [
                'course_enrollments.id',
                'course_enrollments.youth_id',
                'courses.institute_id',
                'courses.program_id',
                'courses.course_id',
                'institutes.training_center_id',
                'institutes.batch_id',
                'courses.payment_status',
                'branches.title as branch_title',
                'branches.title_en as branch_title_en',
                'courses.program_id',
                'programs.title as program_title',
                'programs.title_en as program_title_en',
                'courses.title',
                'courses.title_en',
                'courses.course_fee',
                'courses.duration',
                'courses.overview',
                'courses.overview_en',
                'courses.target_group',
                'courses.target_group_en',
                'courses.objectives',
                'courses.objectives_en',
                'courses.lessons',
                'courses.lessons_en',
                'courses.training_methodology',
                'courses.training_methodology_en',
                'courses.evaluation_system',
                'courses.evaluation_system_en',
                'courses.prerequisite',
                'courses.prerequisite_en',
                'courses.eligibility',
                'courses.eligibility_en',
                'courses.cover_image',
                'courses.application_form_settings',
                'courses.row_status',
                'courses.created_by',
                'courses.updated_by',
                'courses.created_at',
                'courses.updated_at',
                'courses.deleted_at',
            ]
        );

        $coursesBuilder->join("institutes", function ($join) use ($rowStatus) {
            $join->on('courses.institute_id', '=', 'institutes.id')
                ->whereNull('institutes.deleted_at');
            if (is_integer($rowStatus)) {
                $join->where('institutes.row_status', $rowStatus);
            }
        });

        $coursesBuilder->leftJoin("branches", function ($join) use ($rowStatus) {
            $join->on('courses.branch_id', '=', 'branches.id')
                ->whereNull('branches.deleted_at');
            if (is_integer($rowStatus)) {
                $join->where('branches.row_status', $rowStatus);
            }
        });

        $coursesBuilder->leftJoin("programs", function ($join) use ($rowStatus) {
            $join->on('courses.program_id', '=', 'programs.id')
                ->whereNull('programs.deleted_at');
            if (is_integer($rowStatus)) {
                $join->where('programs.row_status', $rowStatus);
            }
        });

        $coursesBuilder->orderBy('courses.id', $order);

        if (is_integer($rowStatus)) {
            $coursesBuilder->where('courses.row_status', $rowStatus);
        }

        if (!empty($titleEn)) {
            $coursesBuilder->where('courses.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($titleBn)) {
            $coursesBuilder->where('courses.title', 'like', '%' . $titleBn . '%');
        }

        if (is_integer($instituteId)) {
            $coursesBuilder->where('courses.institute_id', '=', $instituteId);
        }

        /** @var Collection $courses */
        if (is_int($paginate) || is_int($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $courses = $coursesBuilder->paginate($pageSize);
            $paginateData = (object)$courses->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $courses = $coursesBuilder->get();
        }
        $response['order'] = $order;
        $response['data'] = $courses->toArray()['data'] ?? $courses->toArray();
        $response['_response_status'] = [
            "success" => true,
            "code" => \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function courseEnrollment(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $validated = $this->courseEnrollService->courseEnrollmentValidator($request)->validate();
        try {
            $courseEnroll = $this->courseEnrollService->enrollCourse($validated);
            $this->courseEnrollService->storeEnrollmentAddresses($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentEducations($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentProfessionalInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentGuardianInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentMiscellaneousInfo($validated, $courseEnroll);
            $this->courseEnrollService->storeEnrollmentPhysicalDisabilities($validated, $courseEnroll);

            $this->updateYouthProfileAfterEnrollment($validated);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Course enroll successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param array $data
     * @return PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RequestException
     */
    public function updateYouthProfileAfterEnrollment(array $data)
    {
        $url = clientUrl(BaseModel::YOUTH_CLIENT_URL_TYPE) . 'youth-update-after-course-enrollment';

        return Http::withOptions([
            'verify' => false,
            'timeout' => 60
        ])
            ->post($url, $data)
            ->throw(function ($response, $e) use ($url) {
                Log::debug("Http/Curl call error. Destination:: " . $url . ' and Response:: ', (array)$response);
                return $e;
            })
            ->json();
    }
}
