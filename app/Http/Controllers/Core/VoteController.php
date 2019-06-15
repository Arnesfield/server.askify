<?php

namespace App\Http\Controllers\Core;

use App\Vote;
use Illuminate\Http\Request;
use App\Http\Resources\VoteResource;
use App\Http\Controllers\ResourceController;

class VoteController extends ResourceController
{
    protected $model = Vote::class;
    protected $modelResource = VoteResource::class;

    public function update(Request $request, $id)
    {
        static::restore($request, $id);
        return parent::update($request, $id);
    }
}
