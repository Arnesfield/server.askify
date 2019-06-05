<?php

use App\Utils\DateHumanizer;

if ( ! function_exists('humanizeDate') ) {
    function humanizeDate($model, &$resource, $keys, $info = false) {
        return DateHumanizer::resourceInto($model, $resource, $keys, $info);
    }
}
