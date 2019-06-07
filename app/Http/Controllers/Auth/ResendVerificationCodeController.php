<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ResendVerificationCodeController extends Controller
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

        // send
        $res = $user->sendEmailVerificationCode();
        return $res
            ? jresponse('An email has been sent to verify your account.')
            : jresponse('Unable to send email verification code.', 422);
    }
}
