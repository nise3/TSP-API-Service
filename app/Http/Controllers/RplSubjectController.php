<?php

namespace App\Http\Controllers;

use App\Models\RplSubject;
use App\Services\RplSubjectService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RplSubjectController extends Controller
{
    /**
     * @var RplSubjectService
     */
    public RplSubjectService $rplSubjectService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RplSubjectController constructor.
     * @param RplSubjectService $rplSubjectService
     */

    public function __construct(RplSubjectService $rplSubjectService)
    {
        $this->rplSubjectService = $rplSubjectService;
        $this->startTime = Carbon::now();
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RplSubject::class);

        $filter = $this->rplSubjectService->filterValidator($request)->validate();

        $response = $this->rplSubjectService->getSubjectList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function getPublicList(Request $request): JsonResponse
    {
        $filter = $this->rplSubjectService->filterValidator($request)->validate();

        $response = $this->rplSubjectService->getSubjectList($filter, $this->startTime,false);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * * Display the specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $subject = $this->rplSubjectService->getOneSubject($id);
        $this->authorize('view', $subject);

        $response = [
            "data" => $subject,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', RplSubject::class);

        $validated = $this->rplSubjectService->validator($request)->validate();
        $subject = $this->rplSubjectService->store($validated);

        $response = [
            'data' => $subject,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "RplSubject added successfully",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $subject = RplSubject::findOrFail($id);

        $this->authorize('update', $subject);

        $validated = $this->rplSubjectService->validator($request, $id)->validate();
        $data = $this->rplSubjectService->update($subject, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "RplSubject updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     *Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $subject = RplSubject::findOrFail($id);

        $this->authorize('delete', $subject);

        DB::beginTransaction();
        try {
            $this->rplSubjectService->destroy($subject);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "RplSubject deleted successfully.",
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
