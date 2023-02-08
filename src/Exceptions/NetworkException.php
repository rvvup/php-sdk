<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Exceptions;

use Exception;

class NetworkException extends Exception
{
    // Custom exception for network errors where we assume the request is picked up by Rvvup..
}
