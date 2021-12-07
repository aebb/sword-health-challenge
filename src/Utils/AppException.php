<?php

namespace App\Utils;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppException extends Exception
{
    public function __construct($message = "", $code = Response::HTTP_BAD_REQUEST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
