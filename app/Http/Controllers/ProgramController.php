<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\ProgramService;

/**
 * Class ProgramController
 * @package App\Http\Controllers
 */
class ProgramController extends Controller
{
    /**
     * @var ProgramService
     */
    public ProgramService $programmeService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * ProgramController constructor.
     * @param ProgramService $programmeService
     */
    public function __construct(ProgramService $programmeService)
    {
        $this->programmeService = $programmeService;
        $this->startTime = Carbon::now();
    }

    /**
     * * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Program::class);

        $filter = $this->programmeService->filterValidator($request)->validate();

        $response = $this->programmeService->getProgramList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $program = $this->programmeService->getOneProgramme($id);

        $this->authorize('view', $program);

        $response = [
        "data" => $program,
        "_response_status" => [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ]
    ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Program::class);

        $validatedData = $this->programmeService->validator($request)->validate();
        $data = $this->programmeService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Program added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $program = Program::findOrFail($id);

        $this->authorize('update', $program);

        $validated = $this->programmeService->validator($request, $id)->validate();
        $data = $this->programmeService->update($program, $validated);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Program updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *  *   Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $program = Program::findOrFail($id);

        $this->authorize('delete', $program);

        $this->programmeService->destroy($program);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Program deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->programmeService->getProgrammeTrashList($request, $this->startTime);
        return Response::json($response);
    }

    public function restore(int $id): JsonResponse
    {
        $programme = Program::onlyTrashed()->findOrFail($id);
        $this->programmeService->restore($programme);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Program restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $programme = Program::onlyTrashed()->findOrFail($id);
        $this->programmeService->forceDelete($programme);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Program permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicProgramList(Request $request): JsonResponse
    {
        $filter = $this->programmeService->filterValidator($request)->validate();

        $response = $this->programmeService->getProgramList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
