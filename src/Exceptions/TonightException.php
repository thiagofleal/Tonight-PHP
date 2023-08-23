<?php

namespace Tonight\Exceptions;

use Exception;
use Throwable;

class TonightException extends Exception
{
    public function __construct($message = "Tonight Exception", $code = NULL, Throwable $previous = NULL) {
        if (empty($code)) {
            parent::__construct($message);
        }
        if (!empty($code) && empty($previous)) {
            parent::__construct($message, $code);
        }
        if (!empty($code) && !empty($previous)) {
            parent::__construct($message, $code, $previous);
        }
    }
}