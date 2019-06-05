<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description',
        'deleted_at',
    ];

    protected $dates = ['deleted_at'];

    protected $attributes = [
        'name' => '',
		'description' => null,
    ];
}
