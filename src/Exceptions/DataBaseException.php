<?php

namespace Tonight\Exceptions;

class DataBaseException extends TonightException
{
    public function __construct($message = "DataBase exception") {
        parent::__construct($message);
    }
}