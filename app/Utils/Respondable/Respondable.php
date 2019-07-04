<?php

namespace App\Utils\Respondable;

trait Respondable
{
    //! should have static $responseMessages = [];

    // if $return, returns message instead of response
    public static function rmsg($key, $status = 200, $meta = [], $return = false)
    {
        $msg = static::$responseMessages[$key];

        if ($meta) {
            $msg = array_merge($meta, compact('msg'));
        }

        return $return ? $msg : jresponse($msg, $status);
    }
}
