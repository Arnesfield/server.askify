<?php

if ( ! function_exists('status') ) {
    function status($value, $def = 200) {
        if ($value === null) {
            return 404;
        }

        return $def;
    }
}
