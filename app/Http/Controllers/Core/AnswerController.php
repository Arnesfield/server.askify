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

    public function setBest(Request $request, $id)
    {
        $with = $request->get('with', []);
        $answer = Answer::with($with)->find($id);
        if (!$answer) {
            return jresponse('Answer not found.', 404);
        }

        // FIXME: make sure not to update timestamps!!
        $answer->question->answers()->update(['is_best_at' => null]);
        $res = $answer->update(['is_best_at' => nowDt()]);

        if (!$res) {
            return jresponse('Unable to set answer as "best answer."', 400);
        }

        $msg = 'Answer set as "best answer."';
        $answer = new AnswerResource($answer);
        $answer = $answer->toArray($request);

        return jresponse(compact('msg', 'answer'));
    }

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
