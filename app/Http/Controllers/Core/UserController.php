<?php

namespace App\Http\Controllers\Core;

use App\User;
use App\Http\Resources\UserResource;
use App\Http\Controllers\ResourceController;

class UserController extends ResourceController
{
    protected $model = User::class;
    protected $modelResource = UserResource::class;
}
