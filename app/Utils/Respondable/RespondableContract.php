<?php

namespace App\Utils\Respondable;

interface RespondableContract
{
    // if $return, returns message instead of response
    public static function rmsg($key, $status = 200, $return = false);
}
