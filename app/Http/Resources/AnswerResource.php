<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $formatted = [];

        if ( isset($res['user']) ) {
            $user = new UserResource($this->user);
            $formatted['user'] = $user->toArray($request);
        }
        if ( isset($res['question']) ) {
            $question = new QuestionResource($this->question);
            $formatted['question'] = $question->toArray($request);
        }

        // dates
        humanizeDate($this, $res, [
            'deleted_at',
            'created_at',
            'updated_at'
        ]);

        return array_merge($res, $formatted);
    }
}
