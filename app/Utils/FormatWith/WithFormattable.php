<?php

namespace App\Utils\FormatWith;

interface WithFormattable {
    static function formatWith($with, $meta = []);
    static function formatWithCount($with, $meta = []);
}
