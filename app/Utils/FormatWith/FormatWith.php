<?php

namespace App\Utils\FormatWith;

abstract class FormatWith
{

    public static function format($with, $withs = [], $new = false) {
        $newWith = [];

        foreach ($withs as $key => $cb) {
            # code...
            $index = array_search($key, $with);
            if ($index !== false) {
                unset($with[$index]);
                $with[$key] = $cb;

                // add to $newWith
                // $newWith consists of keys similar with $with and $withs
                $newWith[$key] = $cb;
            }
        }

        return $new ? $newWith : $with;
    }
}
