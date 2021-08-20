<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


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
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response|ResponseAlias
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpResponseException) {
            $errors = [
                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                "message" => "Invalid Request Format",
            ];
            return response()->json($errors);

        } elseif ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $errors = [
                "code" => ResponseAlias::HTTP_NOT_FOUND,
                "message" => "404 not found",
            ];
            return response()->json($errors);
        } elseif ($e instanceof AuthorizationException) {
            $errors = [
                "code" => ResponseAlias::HTTP_FORBIDDEN,
                "message" => "Don't have permission to access",
            ];
            return response()->json($errors);
        } elseif ($e instanceof ValidationException) {
            $errors = [
                "code" => ResponseAlias::HTTP_FORBIDDEN,
                "message" => "Validation Fail",
                'errors' => $e->errors()
            ];
            return response()->json($errors);
        } elseif ($e instanceof BindingResolutionException) {
            $errors = [
                "code" => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Unable to resolve dependency",
            ];
            return response()->json($errors);
        }
        return parent::render($request, $e);

    }
}
