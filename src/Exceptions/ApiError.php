<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Exceptions;

use Exception;

class ApiError extends Exception
{
    public function __construct($message = "", $code = "")
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function getErrorCode(): ?string
    {
        return $this->code;
    }
}
