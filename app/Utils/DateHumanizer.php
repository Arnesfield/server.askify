<?php

namespace App\Utils;

use Carbon\Carbon;

class DateHumanizer {

    // helper to into()
    public static function resourceInto($model, &$resource, $keys, $info = false)
    {
        // $keys should be array
        if ( ! is_array($keys) ) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            static::into($resource, $key, $model->$key, $info);
        }

         return $resource;
    }

    // set date stuff to $resource
    public static function into(&$resource, $key, $date, $info = false)
    {
        if ( isset($resource[$key]) ) {
            $fDate = static::format($date);
    
            $resource[$key . '_common'] = $fDate['common'];
            if ($info) {
                $resource[$key . '_info'] = $fDate;
            }
        }

        return $resource;
    }

    public static function format(Carbon $date)
    {
        $fDate = date('F d, Y h:i A', strtotime($date));
        $human = $date->diffForHumans();

        return [
            'raw' => $date->toDateTimeString(),
            'common' => $fDate,
            'human' => $human,
            'detailed' => "$fDate ($human)"
        ];
    }
}
