<?php

if ( ! function_exists('asset') ) {
    function asset($append = '') {
        return url("static/$append");
    }
}
