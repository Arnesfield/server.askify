<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function index(Request $request)
    {
        $this->validate(
            $request,
            ['email' => 'required|exists:users,email'],
            [
                'email.required' => 'Email is required.',
                'email.exists' => 'The email does not exist.',
            ]
        );

        // find user with the code
        $email = $request->email;
        // assert that user exists bc of validation above lol
        $user = User::where('email', $email)->first();

        $code = str_random(6);
        $user->password = $code;
        $user->save();

        // send
        $res = $user->sendResetPasswordCode($code);
        return $res
            ? jresponse('An email has been sent to reset your account.')
            : jresponse('Unable to send reset password code.', 422);
    }
}
