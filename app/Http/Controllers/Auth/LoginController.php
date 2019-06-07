<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email.',
            'password.required' => 'Password is required.',
        ]);

        // do stuff here
        $email = $request->email;
        $password = $request->password;

        $with = $request->get('with', []);
        $user = User::with($with)->where('email', $email)->first();

        if (
            ! $user ||
            ! $user->checkPassword($password)
        ) {
            return jresponse('Invalid email or password.', 401);
        }

        // if not yet verified
        if (!$user->email_verified_at) {
            return jresponse('Please check your email to verify your account first.', 401);
        }

        $msg = 'Logged in successfully.';
        $user = new UserResource($user);
        return jresponse(compact('msg', 'user'));
    }
}
