<?php

namespace App\Utils\Taggable;

use Illuminate\Http\Request;

interface TaggableContract
{
    public function tags();
    public function syncTags(Request $request, $key = 'tags', $return = false);
}
