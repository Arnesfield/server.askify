<?php

namespace App\Exceptions;

use Exception;

class AppException extends Exception
{
    private $status;

    public function __construct($message = null, $status = 400) {
        $message = $message ?: 'An error has occurred.';
        $status = $status ?: 400;

        parent::__construct($message);

        $this->status = $status;
    }

    // getters
    public function getStatus()
    {
        return $this->status;
    }
}
