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

    protected function makeBest(Request $request, $id, $best = true)
    {
        $with = $request->get('with', []);
        $answer = Answer::with($with)->find($id);
        if (!$answer) {
            return jresponse('Answer not found.', 404);
        }

        // unbest all except curr answer
        $answer->question
            ->answers()
            ->isBest()
            ->where('id', '!=', $answer->id)
            ->get()
            ->each(function($a) {
                $a->setBest(false);
            });

        $res = $answer->setBest($best);

        if (!$res) {
            return jresponse(
                $best
                    ? 'Unable to set "best answer" status.'
                    : 'Unable to remove "best answer" status.',
                400
            );
        }

        $msg = $best ? 'Set as "best answer."' : 'Removed "best answer."';
        $answer = new AnswerResource($answer);
        $answer = $answer->toArray($request);

        return jresponse(compact('msg', 'answer'));
    }

    public function setBest(Request $request, $id)
    {
        return $this->makeBest($request, $id);
    }

    public function removeBest(Request $request, $id)
    {
        return $this->makeBest($request, $id, false);
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
