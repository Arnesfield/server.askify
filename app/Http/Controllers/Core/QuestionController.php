<?php

namespace App\Http\Controllers\Core;

use App\Question;
use App\Http\Resources\QuestionResource;
use App\Http\Controllers\ResourceController;

class QuestionController extends ResourceController
{
    protected $model = Question::class;
    protected $modelResource = QuestionResource::class;
}
