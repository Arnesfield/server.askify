<?php

namespace App\Utils\Voteable;

use Illuminate\Http\Request;

interface VoteableContract
{
    public function votes();
    public function getVotesTotalAttribute();
}
