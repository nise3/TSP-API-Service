<?php

namespace App\Exceptions;


use Illuminate\Http\Client\RequestException;

class HttpErrorException extends RequestException
{

    public function getPreparedMessage(): string
    {
        return $this->prepareMessage($this->response);
    }
}
