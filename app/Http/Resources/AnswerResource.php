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

        // FIXME: this is messy and shouldn't be here, but it works so...
        // check if viewable by user
        $aUser = user($request, false);
        if ($aUser && isset($res['transactions_viewable_count'])) {
            $uid = $aUser->id;
            $viewable = $uid == $res['user_id'] ||
                $res['privated_at'] === null ||
                $res['transactions_viewable_count'] > 0;
            
            $formatted['is_viewable'] = $viewable;
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
