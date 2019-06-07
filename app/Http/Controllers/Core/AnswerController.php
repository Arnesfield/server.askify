<?php

namespace App\Http\Controllers\Core;

use App\Answer;
use App\Http\Resources\AnswerResource;
use App\Http\Controllers\ResourceController;

class AnswerController extends ResourceController
{
    protected $model = Answer::class;
    protected $modelResource = AnswerResource::class;
}
