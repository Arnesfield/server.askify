<?php

use App\Exceptions\AppException;

if ( ! function_exists('error') ) {
    function error($message = null, $status = null) {
        throw new AppException($message, $status);
    }
}
