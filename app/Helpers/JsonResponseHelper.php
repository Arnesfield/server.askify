<?php

if ( ! function_exists('jresponse') ) {
    function jresponse($msg = '') {
        // if string, don't send as json ;)
        return is_string($msg)
            ? response(...func_get_args())
            : response()->json(...func_get_args());
    }
}
