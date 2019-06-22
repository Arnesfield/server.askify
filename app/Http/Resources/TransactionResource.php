<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $formatted = [];

        if ( isset($res['user']) ) {
            $user = new UserResource($this->user);
            $formatted['user'] = $user->toArray($request);
        }
        if ( isset($res['answer']) ) {
            $answer = new AnswerResource($this->answer);
            $formatted['answer'] = $answer->toArray($request);
        }

        // dates
        humanizeDate($this, $res, [
            'approved_at',
            'deleted_at',
            'created_at',
            'updated_at'
        ]);

        return array_merge($res, $formatted);
    }
}
