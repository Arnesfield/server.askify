<?php

namespace App\Http\Resources;

use App\Question;
use App\Answer;

use Illuminate\Http\Resources\Json\JsonResource;

class VoteResource extends JsonResource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $formatted = [];

        if ( isset($res['user']) ) {
            $user = new UserResource($this->user);
            $formatted['user'] = $user->toArray($request);
        }
        if ( isset($res['voteable']) ) {

            if ($this->voteable instanceof Question) {
                $voteable = new QuestionResource($this->voteable);
            } else if ($this->voteable instanceof Answer) {
                $voteable = new AnswerResource($this->voteable);
            }

            $formatted['voteable'] = $voteable->toArray($request);
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
