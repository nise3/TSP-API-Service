<?php


namespace App\Helpers\Classes;


use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class CustomExceptionHandler
{
    private Throwable $exception;

    public function __construct(Throwable $exception, bool $withLog = true)
    {
        $this->exception = $exception;

        if ($withLog) {
            $this->saveToLog();
        }
    }

    public function convertExceptionToArray(): array
    {
        $errors = [
            "code" => $this->getCode(),
            "message" => $this->getMessage(),
        ];

        if ($this->exception instanceof MethodNotAllowedHttpException) {
            $errors = [
                "code" => JsonResponse::HTTP_METHOD_NOT_ALLOWED,
                "message" => "Method Not Allowed",
            ];
        } else if ($this->exception instanceof NotFoundHttpException || $this->exception instanceof ModelNotFoundException) {
            $errors = [
                "code" => JsonResponse::HTTP_NOT_FOUND,
                "message" => "404 Not Found",
            ];
        } else if ($this->exception instanceof Exception) {
            $errors = [
                "code" => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Internal Server Error!",
            ];
        }

        return $errors;
    }

    public function getMessage(): string
    {
        return $this->exception->getMessage();
    }

    public function getCode(): string
    {
        return $this->exception->getCode()!=0? $this->exception->getCode():500;
    }

    public function getFile(): string
    {
        return $this->exception->getFile();
    }

    public function getLine(): int
    {
        return $this->exception->getLine();
    }

    public function getTrace(): array
    {
        return $this->exception->getTrace();
    }

    public function getTraceAsString(): string
    {
        return $this->exception->getTraceAsString();
    }

    public function getPrevious(): Throwable
    {
        return $this->exception->getPrevious();
    }

    public function __toString()
    {
        return $this->exception->getMessage();
    }

    private function saveToLog()
    {
        Log::debug($this->exception->getMessage());
        Log::debug($this->exception->getTraceAsString());
    }
}
