<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $formatted = [];

        if ( isset($res['users']) ) {
            $users = UserResource::collection($this->users);
            $formatted['users'] = $users->toArray($request);
        }
        if ( isset($res['questions']) ) {
            $questions = QuestionResource::collection($this->questions);
            $formatted['questions'] = $questions->toArray($request);
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
