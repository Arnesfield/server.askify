<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class MeController extends Controller
{
    public function index(Request $request)
    {
        $user = user($request);
        $user = new UserResource($user);
        return jresponse($user);
    }
}
