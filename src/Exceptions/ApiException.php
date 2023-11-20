<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct($message = "",$code = "",Throwable $previous = null) {
        parent::__construct($message,0, $previous);
        $this->code = $code;
    }

    public function getErrorCode(): ?string
    {
        return $this->code;
    }
}

