<?php

namespace App\Utils\Voteable;

use App\Vote;

use Illuminate\Http\Request;

trait Voteable
{
    public function getVotesTotalAttribute()
    {
        $upvotes = $this->votes()->whereNotNull('upvoted_at')->count();
        $downvotes = $this->votes()->whereNotNull('downvoted_at')->count();

        // add both
        return $upvotes - $downvotes;
    }
}
