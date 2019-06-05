<?php

namespace App\Exceptions;

use Exception;

class NotAuthException extends Exception
{
    public function __construct()
    {
        parent::__construct('User not authenticated.');
    }
}
