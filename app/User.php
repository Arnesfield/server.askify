<?php

namespace App;

use App\Interfaces\Makeable;
use App\Interfaces\Validateable;
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

class User extends Model
    implements Makeable, Validateable, AuthenticatableContract, AuthorizableContract
{
    use FileUploadable, Respondable;
    use SoftDeletes, Authenticatable, Authorizable;

    protected $fillable = [
        'fname', 'mname', 'lname', 'email', 'avatar', 'password',
        'email_verification_code',
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
        'email_verification_code' => '',
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

    public function verifyEmail($verify = true)
    {
        $d = $verify ? nowDt() : null;
        return $this->update(['email_verified_at' => $d]);
    }

    // mutators

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setEmailVerificationCodeAttribute($value)
    {
        $this->attributes['email_verification_code'] = $value ?: str_random();
    }

    // scopes

    public function scopeWhereCode($query, $code)
    {
        return $query->where('email_verification_code', $code);
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
        // make sure to use the default values as default
        // then override them
        $attr = app(static::class)->getAttributes();
        $data = array_merge($attr, $data);

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

    protected static $validationErrors = [
        'fname.required' => 'First name is required.',
        'lname.required' => 'Last name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'The email must be a valid email address.',
        'email.unique' => 'The email has already been taken.',

        'old_password.required_with' => 'Please enter your old password.',
        'password.required_with' => 'Password is required.',
        'password.min' => 'Password should be at least 6 characters or above.',
        'passconf.required_with' => 'Please confirm your password.',
        'passconf.same' => 'Passwords should be the same.',
    ];

    // Validateable
    public static function getValidationRules($id = null)
    {
        $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'fname' => 'sometimes|required',
                'mname' => 'sometimes',
                'lname' => 'sometimes|required',
                'email' => 'sometimes|required|email|unique:users,email' . $idCond,

                'old_password' => 'sometimes|required_with:password',
                'password' => 'required_with:passconf,old_password|min:6',
                'passconf' => 'sometimes|required_with:password|same:password',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
