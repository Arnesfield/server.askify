<?php

namespace App\Traits;

trait Respondable
{
    //! should have static $responseMessages = [];

    // if $return, returns message instead of response
    public static function rmsg($key, $status = 200, $return = false)
    {
        $msg = static::$responseMessages[$key];
        return $return ? $msg : jresponse($msg, $status);
    }
}
