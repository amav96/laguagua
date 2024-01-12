<?php

namespace App\Exceptions;

use Throwable;

class AppException extends \Exception
{
    protected string $internalCode = "SYSTEM_FAILURE";
    protected string $_message = "Something went wrong";
    protected mixed $data;

    public function __construct(
        string $message = "",
        string $internalCode,
        mixed $data,
        int $code = 0,
        Throwable $previous = null)
    {
        $this->internalCode = $internalCode;
        $this->_message = $message;
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getInternalCode() : string
    {
        return $this->internalCode;
    }

    public function getData() : mixed
    {
        return $this->data;
    }

    public function getAppResponse() : array
    {
        return [
            "code" => $this->internalCode,
            "message" => $this->_message
        ];
    }
}
