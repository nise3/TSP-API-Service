<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

class ApiInfoController extends Controller
{
    const SERVICE_NAME = 'NISE-3 Organization Management API Service';
    const SERVICE_VERSION = 'V1';

    public function apiInfo(): JsonResponse
    {
        $response = [
            'service_name' => self::SERVICE_NAME,
            'service_version' => self::SERVICE_VERSION,
            'lumen_version' => App::version(),
            'module_list' => [
                'Branch',
                'CourseConfig',
                'Course',
                'Institute',
                'Programme',
                'TrainingCenter',
            ],
            'description'=>[
                'It is a institute management api service that manages services related to a institute'
            ]
        ];
        return Response::json($response,JsonResponse::HTTP_OK);
    }
}
