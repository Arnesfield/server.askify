<?php

namespace App\Http\Controllers;

use App\Interfaces\Makeable;
use App\Interfaces\Validateable;
use App\Utils\Respondable\RespondableContract;
use App\Exceptions\NotAnInstanceOfException;

use Illuminate\Http\Request;

abstract class ResourceController extends Controller
{
    protected $model = '';
    protected $modelResource = '';

    public function __construct() {
        // model should be of these types
        NotAnInstanceOfException::check($this->model, [
            Makeable::class,
            Validateable::class,
            RespondableContract::class,
        ]);
    }

    public function index(Request $request)
    {
        $with = $request->get('with', []);
        $users = $this->model::with($with)->latest()->get();
        $users = $this->modelResource::collection($users);
        return jresponse($users);
    }

    public function show(Request $request, $id)
    {
        $model = $this->model;
        $with = $request->get('with', []);
        $user = $model::with($with)->find($id);

        if (!$user) {
            return $model::rmsg('not found', status($user));
        }

        $user = new $this->modelResource($user);
        return jresponse($user);
    }

    public function store(Request $request)
    {
        $model = $this->model;
        $R = $model::getValidationRules();
        $this->validate($request, $R['rules'], $R['errors']);

        $user = $model::makeMe($request);
        return $user
            ? $model::rmsg('create success')
            : $model::rmsg('create fail', 422);
    }

    public function update(Request $request, $id)
    {
        $model = $this->model;
        $user = $model::find($id);
        if (!$user) {
            return $model::rmsg('not found', status($user));
        }
        
        $R = $model::getValidationRules($id);
        $this->validate($request, $R['rules'], $R['errors']);

        $model::makeMe($request, $user);
        return $model::rmsg('update success');
    }

    public function destroy(Request $request, $id)
    {
        $model = $this->model;
        $user = $model::find($id);
        if (!$user) {
            return $model::rmsg('not found', status($user));
        } else if ( ! $user->delete() ) {
            return $model::rmsg('delete fail', 422);
        }

        return $model::rmsg('delete success');
    }

    public function restore(Request $request, $id)
    {
        $model = $this->model;
        $user = $model::withTrashed()->find($id);
        if (!$user) {
            return $model::rmsg('not found', status($user));
        } else if ( ! $user->restore() ) {
            return $model::rmsg('restore fail', 422);
        }

        return $model::rmsg('restore success');
    }
}
