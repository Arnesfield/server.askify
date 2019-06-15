<?php

namespace App\Http\Controllers\Core;

use App\Vote;
use App\Http\Resources\VoteResource;
use App\Http\Controllers\ResourceController;

class VoteController extends ResourceController
{
    protected $model = Vote::class;
    protected $modelResource = VoteResource::class;
}
