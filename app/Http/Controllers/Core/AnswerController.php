<?php

namespace App\Http\Controllers\Core;

use App\Answer;
use App\Question;
use App\Http\Resources\AnswerResource;
use App\Http\Controllers\ResourceController;

use Illuminate\Http\Request;

class AnswerController extends ResourceController
{
    protected $model = Answer::class;
    protected $modelResource = AnswerResource::class;

    public function showAnswers(Request $request, $id, $uid)
    {
        $with = $request->get('with', []);
        $question = Question::find($id);

        $answers = $question
            ->answers()
            ->with($with)
            ->public()
            // ->where('user_id', $uid)
            ->orWhereHas('transactions', function($q) use ($uid) {
                $q->where('user_id', $uid);
            })
            ->get();

        $answers = AnswerResource::collection($answers);
        return jresponse($answers);
    }
}
