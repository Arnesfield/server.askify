<?php

namespace App;

use App\Interfaces\Makeable;
use App\Traits\FileUploadable;
use App\Traits\Respondable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements Makeable, AuthenticatableContract, AuthorizableContract
{
    use Respondable, FileUploadable;
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

    protected static $responseMessages = [
        'not found' => 'Account not found.',

        'create success' => 'Account created.',
        'update success' => 'Account updated.',
        'delete success' => 'Account deleted.',
        'restore success' => 'Account restored.',

        'create fail' => 'Unable to create account.',
        'update fail' => 'Unable to update account.',
        'delete fail' => 'Unable to delete account.',
        'restore fail' => 'Unable to restore account.',
    ];

    // methods

    public function checkPassword($password)
    {
        return Hash::check($password, $this->password);
    }

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
