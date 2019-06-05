<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface Makeable
{
    public static function makeMe(Request $request, $me = null, $extras = []);
}
