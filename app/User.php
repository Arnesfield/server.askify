<?php

namespace App;

use App\Interfaces\Makeable;
use App\Traits\FileUploadable;

use Illuminate\Http\Request;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements Makeable, AuthenticatableContract, AuthorizableContract
{
    use FileUploadable, SoftDeletes, Authenticatable, Authorizable;

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

    // methods

    // relationships

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // override

    // Makeable
    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();

        if ($me === null) {
            $me = static::create($data);
        } else {
            $me->update($data);
        }

        // relationships
        $ids = requestGetArray($request, 'roles');
        if ($ids !== false) {
            $me->roles()->sync($ids);
        }

        // upload
        $me->uploadImage($request, 'avatar');

        return $me;
    }
}
