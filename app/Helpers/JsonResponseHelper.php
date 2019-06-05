<?php

if ( ! function_exists('jresponse') ) {
    function jresponse() {
        return response()->json(...func_get_args());
    }
}
