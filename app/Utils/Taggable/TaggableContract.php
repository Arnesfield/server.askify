<?php

namespace App\Utils\Taggable;

interface TaggableContract
{
    public function tags();
    public function syncTags(Request $request, $key = 'tags', $return = false);
}
