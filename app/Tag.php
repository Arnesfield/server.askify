<?php

namespace App;

use App\Utils\FileUploadable\FileUploadable;
use App\Utils\FileUploadable\FileUploadableContract;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends CommonModel implements FileUploadableContract
{
    use FileUploadable;
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'img_src',
        'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $attributes = [
        'name' => '',
        'description' => null,
        'img_src' => null,
		'deleted_at' => null,
    ];

    protected static $responseMessages = [
        'not found' => 'Tag not found.',

        'create success' => 'Tag posted.',
        'update success' => 'Tag updated.',
        'delete success' => 'Tag deleted.',
        'restore success' => 'Tag restored.',

        'create fail' => 'Unable to post tag.',
        'update fail' => 'Unable to update tag.',
        'delete fail' => 'Unable to delete tag.',
        'restore fail' => 'Unable to restore tag.',
    ];

    // methods

    // mutators

    // scopes

    // relationships

    public function users()
    {
        return $this->morphedByMany('App\User', 'taggable');
    }

    public function questions()
    {
        return $this->morphedByMany('App\Question', 'taggable');
    }

    // override

    // Makeable
    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();

        if ($me === null) {
            $me = new static($data);
        } else {
            $me->update($data);
        }

        // upload
        $me->uploadImage($request, 'img_src');

        return $me;
    }

    protected static $validationErrors = [
        'name.required' => 'Name is required.',
        'name.unique' => 'This name is already taken.',
        // 'img_src.image' => 'Uploaded item should be an image.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'name' => 'sometimes|required|unique:tags,name' . $idCond,
                'description' => 'sometimes',
                // 'img_src' => 'sometimes|image',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
