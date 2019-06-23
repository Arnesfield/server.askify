<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $res = parent::toArray($request);
        $formatted = [];

        if ( isset($res['questions']) ) {
            $questions = QuestionResource::collection($this->questions);
            $formatted['questions'] = $questions->toArray($request);
        }
        if ( isset($res['answers']) ) {
            $answers = AnswerResource::collection($this->answers);
            $formatted['answers'] = $answers->toArray($request);
        }
        if ( isset($res['roles']) ) {
            $roles = RoleResource::collection($this->roles);
            $formatted['roles'] = $roles->toArray($request);

            // also add ids of roles
            $formatted['auth'] = $this->roles->pluck('id');
        }
        if ( isset($res['tags']) ) {
            $tags = TagResource::collection($this->tags);
            $formatted['tags'] = $tags->toArray($request);
        }

        // dates
        humanizeDate($this, $res, [
            'email_verified_at',
            'deleted_at',
            'created_at',
            'updated_at'
        ]);

        return array_merge($res, $formatted);
    }
}
