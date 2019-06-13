<?php

namespace App\Http\Controllers\Core;

use App\Tag;
use App\Http\Resources\TagResource;
use App\Http\Controllers\ResourceController;

class TagController extends ResourceController
{
    protected $model = Tag::class;
    protected $modelResource = TagResource::class;
}
