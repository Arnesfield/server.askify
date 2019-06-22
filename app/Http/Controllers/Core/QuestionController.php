<?php

namespace App\Http\Controllers\Core;

use App\User;
use App\Answer;
use App\Question;
use App\Http\Resources\QuestionResource;
use App\Http\Controllers\ResourceController;

use Illuminate\Http\Request;

class QuestionController extends ResourceController
{
    protected $model = Question::class;
    protected $modelResource = QuestionResource::class;
    
    public function showAnswers(Request $request, $id, $uid)
    {
        $question = Question::find($id);

        $questions = $question
            ->answers()
            ->public()
            // ->where('user_id', $uid)
            ->orWhereHas('transactions', function($q) use ($uid) {
                $q->where('user_id', $uid);
            })
            ->get();

        $questions = QuestionResource::collection($questions);
        return jresponse($questions);
    }
}
