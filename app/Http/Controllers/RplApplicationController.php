<?php

namespace App\Http\Controllers;

use App\Models\RplApplication;
use App\Models\RplOccupation;
use App\Services\RplApplicationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplApplicationController extends Controller
{
    /**
     * @var RplApplicationService
     */
    public RplApplicationService $rplApplicationService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplOccupationController constructor.
     * @param RplApplicationService $rplApplicationService
     */

    public function __construct(RplApplicationService $rplApplicationService)
    {
        $this->rplApplicationService = $rplApplicationService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException|ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RplApplication::class);
        $filter = $this->rplApplicationService->filterValidator($request)->validate();

        $response = $this->rplApplicationService->getRplApplicationList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rplApplicationService->filterValidator($request)->validate();

        $response = $this->rplApplicationService->getRplApplicationList($filter, $this->startTime,true);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $rplApplication = $this->rplApplicationService->getOneRplApplication($id);
        $this->authorize('view', $rplApplication);

        $response = [
            "data" => $rplApplication,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getRplApplicationDetails(Request $request, int $id): JsonResponse
    {
        $rplApplication = $this->rplApplicationService->getOneRplApplication($id);
        $response = [
            "data" => $rplApplication,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a Rpl Assessment
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function createRplAssessment(Request $request): JsonResponse
    {
        // $this->authorize('create', RplApplication::class); // not needed for public
        $validated = $this->rplApplicationService->validator($request)->validate();
        $validated['application_status'] = RplApplication::APPLICATION_STATUS_ASSESSMENT_COMPLETED;
        $answers = $this->rplApplicationService->answersValidator($request)->validate();
        $rplApplication = $this->rplApplicationService->store($validated);
        $rplApplication = $this->rplApplicationService->updateResult($rplApplication, $answers);

        $response = [
            'data' => $rplApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Rpl assessment added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Create a Rpl Application
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    //TODO: This commented method is converted to createRplApplication() method bellow
    /*public function store(Request $request): JsonResponse
    {
        $rplApplication = RplApplication::findOrFail($request->input('id'));
        $validated = $this->rplApplicationService->validator($request)->validate();
        $validated['application_status'] = RplApplication::APPLICATION_STATUS_APPLICATION_SUBMITTED;
        $rplApplication = $this->rplApplicationService->storeApplication($rplApplication, $validated);
        $response = [
            'data' => $rplApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Rpl assessment  added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }*/

    /**
     * Create a Rpl Application
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function createRplApplication(Request $request): JsonResponse
    {
        $rplApplication = RplApplication::findOrFail($request->input('id'));
        $validated = $this->rplApplicationService->validator($request)->validate();
        $validated['application_status'] = RplApplication::APPLICATION_STATUS_APPLICATION_SUBMITTED;
        $rplApplication = $this->rplApplicationService->storeApplication($rplApplication, $validated);
        $response = [
            'data' => $rplApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Rpl assessment  added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rplApplication = RplApplication::findOrFail($id);

        $this->authorize('update', $rplApplication);

        $validated = $this->rplApplicationService->validator($request, $id)->validate();
        $data = $this->rplApplicationService->update($rplApplication, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth assessment updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function assignToBatch(Request $request, int $id): JsonResponse
    {
        $rplApplication = RplApplication::findOrFail($id);

        $this->authorize('update', $rplApplication);

        $validated = $this->rplApplicationService->assignToBatchValidator($request, $id)->validate();
        $data = $this->rplApplicationService->update($rplApplication, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Youth assessment assigned to batch successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $rplApplication = RplApplication::findOrFail($id);

        $this->authorize('delete', $rplApplication);

        DB::beginTransaction();
        try {
            $this->rplApplicationService->destroy($rplApplication);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Youth Assessment deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
