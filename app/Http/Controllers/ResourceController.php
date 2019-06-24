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
        $withCount = $request->get('withCount', []);
        $where = $request->get('where', []);

        $items = $this->model::with($with)
            ->withCount($withCount)
            ->where($where)
            ->latest()
            ->get();

        $items = $this->modelResource::collection($items);
        return jresponse($items);
    }

    public function show(Request $request, $id)
    {
        $model = $this->model;
        $with = $request->get('with', []);
        $withCount = $request->get('withCount', []);

        $item = $model::with($with)
            ->withCount($withCount)
            ->find($id);

        if (!$item) {
            return $model::rmsg('not found', status($item));
        }

        $item = new $this->modelResource($item);
        return jresponse($item);
    }

    public function store(Request $request)
    {
        $model = $this->model;
        $R = $model::getValidationRules();
        $this->validate($request, $R['rules'], $R['errors']);

        $item = $model::makeMe($request);
        return $item
            ? $model::rmsg('create success')
            : $model::rmsg('create fail', 422);
    }

    public function update(Request $request, $id)
    {
        $model = $this->model;
        $item = $model::find($id);
        if (!$item) {
            return $model::rmsg('not found', status($item));
        }
        
        $R = $model::getValidationRules($id);
        $this->validate($request, $R['rules'], $R['errors']);

        $model::makeMe($request, $item);
        return $model::rmsg('update success');
    }

    public function destroy(Request $request, $id)
    {
        $model = $this->model;
        $item = $model::find($id);
        if (!$item) {
            return $model::rmsg('not found', status($item));
        } else if ( ! $item->delete() ) {
            return $model::rmsg('delete fail', 422);
        }

        return $model::rmsg('delete success');
    }

    public function restore(Request $request, $id)
    {
        $model = $this->model;
        $item = $model::withTrashed()->find($id);
        if (!$item) {
            return $model::rmsg('not found', status($item));
        } else if ( ! $item->restore() ) {
            return $model::rmsg('restore fail', 422);
        }

        return $model::rmsg('restore success');
    }
}
