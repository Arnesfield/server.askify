<?php

namespace App\Http\Controllers\Core;

use App\Question;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;

use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $with = $request->get('with', []);
        $questions = Question::with($with)->latest()->get();
        $questions = QuestionResource::collection($questions);
        return jresponse($questions);
    }

    public function show(Request $request, $id)
    {
        $with = $request->get('with', []);
        $question = Question::with($with)->find($id);

        if (!$question) {
            return Question::rmsg('not found', status($question));
        }

        $question = new QuestionResource($question);
        return jresponse($question);
    }

    public function store(Request $request)
    {
        $R = Question::getValidationRules();
        $this->validate($request, $R['rules'], $R['errors']);

        $question = Question::makeMe($request);
        return $question
            ? Question::rmsg('create success')
            : Question::rmsg('create fail', 422);
    }

    public function update(Request $request, $id)
    {
        $question = Question::find($id);
        if (!$question) {
            return Question::rmsg('not found', status($question));
        }
        
        $R = Question::getValidationRules($id);
        $this->validate($request, $R['rules'], $R['errors']);

        Question::makeMe($request, $question);
        return Question::rmsg('update success');
    }

    public function destroy(Request $request, $id)
    {
        $question = Question::find($id);
        if (!$question) {
            return Question::rmsg('not found', status($question));
        } else if ( ! $question->delete() ) {
            return Question::rmsg('delete fail', 422);
        }

        return Question::rmsg('delete success');
    }

    public function restore(Request $request, $id)
    {
        $question = Question::withTrashed()->find($id);
        if (!$question) {
            return Question::rmsg('not found', status($question));
        } else if ( ! $question->restore() ) {
            return Question::rmsg('restore fail', 422);
        }

        return Question::rmsg('restore success');
    }
}
