<?php

namespace App;

use App\Utils\FileUploadable\FileUploadable;
use App\Utils\FileUploadable\FileUploadableContract;
use App\Utils\Taggable\Taggable;
use App\Utils\Taggable\TaggableContract;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends CommonModel implements TaggableContract, FileUploadableContract
{
    use Taggable, FileUploadable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title', 'content', 'img_src',
        'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $attributes = [
        'user_id' => null,
        'title' => '',
        'content' => '',
        'img_src' => null,
		'deleted_at' => null,
    ];

    protected static $responseMessages = [
        'not found' => 'Question not found.',

        'create success' => 'Question posted.',
        'update success' => 'Question updated.',
        'delete success' => 'Question deleted.',
        'restore success' => 'Question restored.',

        'create fail' => 'Unable to post question.',
        'update fail' => 'Unable to update question.',
        'delete fail' => 'Unable to delete question.',
        'restore fail' => 'Unable to restore question.',
    ];

    // methods

    // mutators

    // scopes

    // relationships

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function votes()
    {
        return $this->morphMany('App\Vote', 'voteable');
    }

    // override

    // Makeable
    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();

        if ($me === null) {
            $me = new static($data);
            user($request)->questions()->save($me);
        } else {
            $me->update($data);
        }

        // relationships
        $me->syncTags($request);

        // upload
        $me->uploadImage($request, 'img_src');

        return $me;
    }

    protected static $validationErrors = [
        'title.required' => 'Title is required.',
        'content.required' => 'Content or description is required.',
        // 'img_src.image' => 'Uploaded item should be an image.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        // $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'title' => 'sometimes|required',
                'content' => 'sometimes|required',
                // 'img_src' => 'sometimes|image',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
