<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $R = User::getValidationRules(null, ['roles' => '3,4']);
        $this->validate($request, $R['rules'], $R['errors']);

        $user = User::makeMe($request);
        if (!$user) {
            return jresponse('Unable to register your account.', 422);
        }

        // TODO: send mail
        return jresponse('Account registered. An email has been sent to verify your account.');
    }
}
