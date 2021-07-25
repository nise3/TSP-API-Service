<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Throwable;


class BranchController extends Controller
{
    /**
     * @var BranchService
     */
    public BranchService $branchService;
    private \Carbon\Carbon $startTime;

    /**
     * BranchController constructor.
     * @param BranchService $branchService
     */
    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
        $this->startTime = Carbon::now();
    }

    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->branchService->getBranchList($request);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => \Carbon\Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response);
    }
}
