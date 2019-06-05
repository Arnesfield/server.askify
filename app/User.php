<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, Authenticatable, Authorizable;

    protected $fillable = [
        'fname', 'mname', 'lname', 'email', 'avatar', 'password',
        'email_verified_at', 'deleted_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $dates = [
        'email_verified_at', 'deleted_at',
    ];

    protected $attributes = [
        'fname' => '',
		'mname' => null,
		'lname' => '',
		'email' => '',
		'avatar' => null,
		'password' => '',
        'email_verified_at' => null,
		'deleted_at' => null,
    ];

    // relationships

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
}
