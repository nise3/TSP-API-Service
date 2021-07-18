<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class ExampleController extends Controller
{
    public function hateoasResponse()
    {
        // multiple data
        $response = [
            "data" => [
                [],
                []
            ],
            "_response_status" => [
                "success" => true,
                "code" => 200,
                "message" => "Job finished successfully.",
                "started" => "2017-01-12T18:43:42.566Z",
                "finished" => "2017-01-12T18:43:42.597Z",
            ],
            "_links" => [

            ],
            "_page" => [
                "size" => 10,
                "total_element" => 200,
                "total_page" => 1,
                "current_page" => 1
            ],
            "_order" => 'asc'
        ];

        // single data.
        $response = [
            "name" => "Hasan",
            "age" => 60,
            "_response_status" => [
                "success" => true,
                "code" => 200,
                "message" => "Job finished successfully.",
                "started" => "2017-01-12T18:43:42.566Z",
                "finished" => "2017-01-12T18:43:42.597Z",
            ],
            "_links" => [

            ]
        ];

        return Response::json($response, 201);
    }
}
