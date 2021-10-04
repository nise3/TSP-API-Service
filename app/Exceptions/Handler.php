<?php

namespace App\Exceptions;

use BadMethodCallException;
use ErrorException;
use Exception;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\RequestException as IlluminateRequestException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ParseError;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TypeError;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        $errors = [
            '_response_status' => [
                'success' => false,
                'code' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Unknown Error",
                "query_time" => 0
            ]
        ];

        if ($e instanceof HttpResponseException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_BAD_REQUEST;
            $errors['_response_status']['message'] = "Invalid Request Format";
        } elseif ($e instanceof AuthorizationException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_UNAUTHORIZED;
            $errors['_response_status']['message'] = "Unable to Access";
        } elseif ($e instanceof ValidationException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $errors['_response_status']['message'] = "Validation Error";
            $errors['errors'] = $e->errors();
        } elseif ($e instanceof BindingResolutionException) {
            $errors['_response_status']['message'] = "Binding Resolution Error";
        } else if ($e instanceof CoreApiCallException) {
            $errors['_response_status']['message'] = "Core Service API Call Failed.";
        } else if ($e instanceof YouthApiCallException) {
            $errors['_response_status']['message'] = "Youth Service API Call Failed.";
        } else if ($e instanceof OrganizationApiCallException) {
            $errors['_response_status']['message'] = "Industry/Organization Service API Call Failed.";
        } else if ($e instanceof IlluminateRequestException || $e instanceof \GuzzleHttp\Exception\RequestException) {
            $errors['_response_status']['message'] = "External API Call Failed.";
        } elseif ($e instanceof ModelNotFoundException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
            $errors['_response_status']['message'] = 'Entry or Row for ' . str_replace('App\\', '', $e->getModel()) . ' was not Found'; //$e->getMessage();
        } elseif ($e instanceof NotFoundHttpException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
            $errors['_response_status']['message'] = $e->getMessage();
        } elseif ($e instanceof BadMethodCallException) {
            $errors['_response_status']['message'] = "Bad Method has been Called";
        } elseif ($e instanceof ErrorException) {
            $errors['_response_status']['message'] = "Internal Server Side Error";
        } elseif ($e instanceof TypeError) {
            $errors['_response_status']['message'] = "Type Error";
        } elseif ($e instanceof ParseError) {
            $errors['_response_status']['message'] = "Parsing Error";
        } elseif ($e instanceof Exception) {
            $errors['_response_status']['message'] = $e->getMessage();
        }

        return response()->json($errors);

    }
    /**
     * Render an exception into an HTTP response.
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response|ResponseAlias
     * @throws Throwable
     */
/*    public function render1(Request $request, Throwable $e)
    {

        if ($e instanceof HttpResponseException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                "message" => "Invalid Request Format",
                "query_time" => 0
            ];
            return response()->json($errors);

        } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_NOT_FOUND,
                "message" => "Method not found",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof AuthorizationException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_UNAUTHORIZED,
                "message" => "Unable to Access",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ValidationException) {
            $errors['errors'] = $e->errors();
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                "message" => "validation Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof BindingResolutionException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Binding Resolution Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ErrorException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Internal Server Side Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof TypeError) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Type Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof ParseError) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Parsing Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        } elseif ($e instanceof BadMethodCallException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Call a Bad Method",
                "query_time" => 0
            ];
            return response()->json($errors);
        }
        elseif ($e instanceof RequestException) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_CONFLICT,
                "message" => $e->getMessage(),
                "query_time" => 0
            ];
            return response()->json($errors);
        }
        elseif ($e instanceof Exception) {
            $errors['_response_status'] = [
                'success' => false,
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Internal Server Error",
                "query_time" => 0
            ];
            return response()->json($errors);
        }

        return parent::render($request, $e);
    }*/
}
