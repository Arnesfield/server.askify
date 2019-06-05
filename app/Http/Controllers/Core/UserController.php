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
            return User::rmsg('not found', status($user));
        }

        $user = new UserResource($user);
        return jresponse($user);
    }

    public function store(Request $request)
    {
        $user = User::makeMe($request);
        return $user
            ? User::rmsg('create success')
            : User::rmsg('create fail', 422);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return User::rmsg('not found', status($user));
        }

        User::makeMe($request, $user);
        return User::rmsg('update success');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return User::rmsg('not found', status($user));
        } else if ( ! $user->delete() ) {
            return User::rmsg('delete fail', 422);
        }

        return User::rmsg('delete success');
    }

    public function restore(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);
        if (!$user) {
            return User::rmsg('not found', status($user));
        } else if ( ! $user->restore() ) {
            return User::rmsg('restore fail', 422);
        }

        return User::rmsg('restore success');
    }
}
