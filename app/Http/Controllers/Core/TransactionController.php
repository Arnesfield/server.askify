<?php

namespace App\Http\Controllers\Core;

use App\Transaction;
use App\Http\Resources\TransactionResource;
use App\Http\Controllers\ResourceController;

class TransactionController extends ResourceController
{
    protected $model = Transaction::class;
    protected $modelResource = TransactionResource::class;
}
