<?php

namespace App\Exceptions;

use Throwable;

class BussinessException extends AppException
{
    public function __construct($message = "", $internalCode="", $data=[], Throwable $previous = null)
    {
        parent::__construct($message, $internalCode, $data, 0, $previous);
    }
}
