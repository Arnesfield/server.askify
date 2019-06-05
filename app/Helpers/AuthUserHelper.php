<?php

use App\User;
use App\Http\Resources\UserResource;
use App\Exceptions\NotAuthException;

use Illuminate\Http\Request;

if ( ! function_exists('user') ) {
    function user(Request $request, $error = true) {
        $id = $request->get('authId');
        $user = null;

        if ($id) {
            $with = $request->get('authWith', []);
            $user = User::with($with)->find($id);
        }

        // return here
        // if $error is allowed and there is no $user,
        // throw some exception hehe ;)
        if ($error && !$user) {
            throw new NotAuthException;
        }

        return $user;
    }
}
