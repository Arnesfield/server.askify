<?php

use Illuminate\Http\Request;

if ( ! function_exists('requestGetArray') ) {
    function requestGetArray(Request $request, $key) {
        $val = $request->get($key, false);
        if ($val !== false) {
            // val should be array
            $val = !is_array($val) ? [$val] : $val;
            // if value is only '', change to []
            $val = count($val) === 1 && $val[0] === '' ? [] : $val;
        }

        return $val;
    }
}
