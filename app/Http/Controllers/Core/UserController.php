<?php

namespace App\Http\Controllers\Core;

use App\User;
use App\Question;
use App\Core\QuestionFeed;
use App\Http\Resources\UserResource;
use App\Http\Controllers\ResourceController;

use Illuminate\Http\Request;

class UserController extends ResourceController
{
    protected $model = User::class;
    protected $modelResource = UserResource::class;

    public function questionFeed(Request $request, $id)
    {
        // user should be an expert?
        $roles = $request->get('roles', 4);
        $user = User::whereRoles($roles)->find($id);

        if (!$user) {
            return jresponse([]);
        }

        $with = $request->get('with', []);
        $builder = Question::with($with);

        $qf = new QuestionFeed($user);
        $res = $qf->get($builder);

        return jresponse($res);
    }
}
