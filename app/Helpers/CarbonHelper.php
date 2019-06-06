<?php

use Carbon\Carbon;

if ( ! function_exists('now') ) {
    function now() {
        return Carbon::now();
    }
}

if ( ! function_exists('nowDt') ) {
    function nowDt() {
        return Carbon::now()->toDateTimeString();
    }
}
