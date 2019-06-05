<?php

namespace App\Http\Controllers\Core;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $with = $request->get('with', []);
        $users = User::with($with)->get();
        $users = UserResource::collection($users);
        return jresponse($users);
    }

    public function show(Request $request, $id)
    {
        $with = $request->get('with', []);
        $user = User::with($with)->find($id);

        if (!$user) {
            return jresponse('User not found.', status($user));
        }

        $user = new UserResource($user);
        return jresponse($user);
    }
}
