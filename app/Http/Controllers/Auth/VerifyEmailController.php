<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function index(Request $request)
    {
        $this->validate(
            $request,
            ['c' => 'required'],
            ['c.required' => 'No verification code found.']
        );

        // find user with the code
        $code = $request->c;
        $user = User::whereCode($code)->first();

        if (!$user) {
            return jresponse('Invalid verification code.', 401);
        } else if ($user->email_verified_at) {
            // if already verified, just ugh
            return jresponse('Account is already verified.');
        }

        $res = $user->verifyEmail();
        return $res
            ? jresponse('Account email verified. You may now login.')
            : jresponse('Unable to verify account.', 422);
    }
}
