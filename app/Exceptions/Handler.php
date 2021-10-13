<?php

namespace App\Exceptions;

//use ErrorException;
use BadMethodCallException;
use ErrorException;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException as IlluminateRequestException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ParseError;
use PDOException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
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
            $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
            $errors['_response_status']['message'] = "Invalid Request Format";
        } elseif ($e instanceof AuthorizationException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_FORBIDDEN;
            $errors['_response_status']['message'] = "Unable to Access";
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_METHOD_NOT_ALLOWED;
            $errors['_response_status']['message'] = "Http Request Method Not Allowed";
        } elseif ($e instanceof ValidationException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
            $errors['_response_status']['message'] = "Validation Error";
            $errors['errors'] = $e->errors();
        } elseif ($e instanceof BindingResolutionException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            $errors['_response_status']['message'] = "Binding Resolution Error";
        } else if ($e instanceof IlluminateRequestException || $e instanceof RequestException) {
            $errors=idUserErrorMessage($e);
        } elseif ($e instanceof ModelNotFoundException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
            $errors['_response_status']['message'] = 'Entry or Row for ' . str_replace('App\\', '', $e->getModel()) . ' was not Found'; //$e->getMessage();
        } elseif ($e instanceof NotFoundHttpException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
            $errors['_response_status']['message'] = "dkdkkd";
        } elseif ($e instanceof BadMethodCallException) {
            $errors['_response_status']['message'] = "Bad Method has been Called";
        } elseif ($e instanceof ErrorException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            $errors['_response_status']['message'] = "Internal Server Side Error";
        } elseif ($e instanceof TypeError) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_UNSUPPORTED_MEDIA_TYPE;
            $errors['_response_status']['message'] = "Type Error";
        } elseif ($e instanceof ParseError) {
            $errors['_response_status']['message'] = "Parsing Error";
        } elseif ($e instanceof PDOException) {
            $errors['_response_status']['code'] = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            $errors['_response_status']['message'] = "PDO Error";
        } elseif ($e instanceof Exception) {
            $errors['_response_status']['code'] = $e->getCode() ?? ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            $errors['_response_status']['message'] = "dhdhhdhdhdhhd";
        }
        return response()->json($errors, $errors['_response_status']['code']);

    }
}
