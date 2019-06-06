<?php

namespace App;

use App\Interfaces\Makeable;
use App\Interfaces\Validateable;
use App\Traits\FileUploadable;
use App\Traits\Respondable;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model implements Makeable, Validateable
{
    use FileUploadable, Respondable;
    use SoftDeletes;

    protected $fillable = [
        'title', 'content', 'img_src',
        'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $attributes = [
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
        return $this->belongsTo(User::class);
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
        // TODO: to follow tags
        $ids = requestGetArray($request, 'tags');
        if ($ids !== false) {
            $me->tags()->sync($ids);
        }

        // upload
        $me->uploadImage($request, 'img_src');

        return $me;
    }

    protected static $validationErrors = [
        'title.required' => 'Title is required.',
        'content.required' => 'Content or description is required.',
        'img_src.image' => 'Uploaded item should be an image.',

        'tags.array' => 'Unable to read tags.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        // $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'title' => 'sometimes|required',
                'content' => 'sometimes|required',
                'img_src' => 'sometimes|image',

                'tags' => 'sometimes|array',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
