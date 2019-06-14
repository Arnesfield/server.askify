<?php

namespace App;

use App\Mail\EmailVerification;
use App\Utils\FileUploadable\FileUploadable;
use App\Utils\FileUploadable\FileUploadableContract;
use App\Utils\Taggable\Taggable;
use App\Utils\Taggable\TaggableContract;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User
    extends CommonModel
    implements
        TaggableContract,
        FileUploadableContract,
        AuthenticatableContract,
        AuthorizableContract
{
    use Taggable, FileUploadable;
    use SoftDeletes, Authenticatable, Authorizable;

    protected $appends = [
        'fullname',
    ];

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

    public function fullname($withMiddle = true)
    {
        $name = $this->fname . ' ';
        $name .= $withMiddle && $this->mname ? $this->mname . ' ' : '';
        $name .= $this->lname;

        return $name;
    }

    public function checkPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public function verifyEmail($verify = true)
    {
        $d = $verify ? nowDt() : null;
        return $this->update(['email_verified_at' => $d]);
    }

    public function setVerificationCode($code = null, $force = false)
    {
        $code = $code ?: str_random();
        // check if exists
        $attrCode = $this->original['email_verification_code'];
        if (!$force && $attrCode) {
            return false;
        }

        // change it
        $this->attributes['email_verification_code'] = $code;
        return true;
    }

    public function sendEmailVerificationCode()
    {
        $email = new EmailVerification($this);
        Mail::to($this)->send($email);

        return count(Mail::failures()) === 0;
    }

    // accessors

    public function getFullnameAttribute()
    {
        return $this->fullName();
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

    public function scopeWhereRoles($query, $roles = [])
    {
        // should be an array
        $roles = is_numeric($roles) ? [$roles] : $roles;

        if ($roles) {
            $query->whereHas('roles', function($q) use ($roles) {
                $q->whereIn('roles.id', $roles);
            });
        }

        return $query;
    }

    // relationships

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'user_roles');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function tags()
    {
        // preferences
        return $this->morphToMany('App\Tag', 'taggable');
    }

    // override

    // Makeable
    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();

        if ($me === null) {
            $data['email_verification_code'] = '';
            $me = static::create($data);
        } else {
            $me->update($data);
        }

        // relationships
        $ids = requestGetArray($request, 'roles');
        if ($ids !== false) {
            $me->roles()->sync($ids);
        }

        $me->syncTags($request);

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
        // 'avatar.required' => 'Avatar is required.',
        // 'avatar.image' => 'Avatar should be an image.',

        'old_password.required_with' => 'Please enter your old password.',
        'password.required_with' => 'Password is required.',
        'password.min' => 'Password should be at least 6 characters or above.',
        'passconf.required_with' => 'Please confirm your password.',
        'passconf.same' => 'Passwords should be the same.',

        'roles.array' => 'Select an account type.',
        'roles.in' => 'Account type is invalid.',
    ];

    protected static $validationMeta = [
        'roles' => '1,2,3,4'
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        $idCond = $id ? ",$id" : '';
        $meta = array_merge(static::$validationMeta, $meta);

        return [
            'rules' => [
                'fname' => 'sometimes|required',
                'mname' => 'sometimes',
                'lname' => 'sometimes|required',
                'email' => 'sometimes|required|email|unique:users,email' . $idCond,
                'avatar' => 'sometimes', // required|image

                'old_password' => 'sometimes|required_with:password',
                'password' => 'required_with:passconf,old_password|min:6',
                'passconf' => 'sometimes|required_with:password|same:password',

                'roles' => 'sometimes|array|in:' . $meta['roles'],
            ],
            'errors' => static::$validationErrors
        ];
    }
}
