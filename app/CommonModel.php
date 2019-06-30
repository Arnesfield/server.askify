<?php

namespace App;

use App\Interfaces\Makeable;
use App\Interfaces\Validateable;

use App\Utils\Respondable\Respondable;
use App\Utils\Respondable\RespondableContract;

use App\Traits\DateLatest;

use Illuminate\Database\Eloquent\Model;

abstract class CommonModel
    extends Model
    implements Makeable, Validateable, RespondableContract
{
    use DateLatest, Respondable;
}
