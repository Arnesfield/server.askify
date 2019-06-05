<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class MeController extends Controller
{
    public function index(Request $request)
    {
        $user = user($request);
        return jresponse($user);
    }
}
