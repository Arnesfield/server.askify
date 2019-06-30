<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DateLatest
{

    public function scopeDateLatest($query)
    {
        return $query
            ->orderBy('updated_at', 'DESC')
            ->orderBy('created_at', 'DESC');
    }
}
