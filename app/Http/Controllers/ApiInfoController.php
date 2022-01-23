<?php

namespace App\Http\Controllers;


use App\Helpers\Classes\FileHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ApiInfoController extends Controller
{
    const SERVICE_NAME = 'NISE-3 Institute Management API Service';
    const SERVICE_VERSION = 'V1';

    public function apiInfo(): JsonResponse
    {
        $response = [
            'service_name' => self::SERVICE_NAME,
            'service_version' => self::SERVICE_VERSION,
            'lumen_version' => App::version(),
            'module_list' => [
                'Branch',
                'Batch',
                'Course',
                'Institute',
                'Program',
                'TrainingCenter',
                'Trainer',
                'CourseEnrollment'
            ],
            'description' => [
                'It is a institute management api service that manages services related to a institute'
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function fileUpload(Request $request)
    {
        try {
            $directory = 'uploads/' . date('Y/F');
            $fileHandler = new FileHandler();
            $path = $fileHandler->storePhoto($request->file, $directory);
            $response = [
                "data" => url('/') . '/api/v1/' . $path,
                '_response_status' => [
                    "success" => true
                ]
            ];
        } catch (Throwable $e) {
            echo "<pre>";
            print_r($e->getMessage());
            echo "</pre>";
            die;
        }
        return Response::json($response);
    }
}
