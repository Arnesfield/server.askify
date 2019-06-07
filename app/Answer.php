<?php

namespace App;

use App\Utils\FileUploadable\FileUploadable;
use App\Utils\FileUploadable\FileUploadableContract;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends CommonModel implements FileUploadableContract
{
    use FileUploadable;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'question_id',
        'content', 'img_src',
        'deleted_at',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $attributes = [
        'user_id' => null,
        'question_id' => null,
        'content' => '',
        'img_src' => null,
		'deleted_at' => null,
    ];

    protected static $responseMessages = [
        'not found' => 'Answer not found.',

        'create success' => 'Answer posted.',
        'update success' => 'Answer updated.',
        'delete success' => 'Answer deleted.',
        'restore success' => 'Answer restored.',

        'create fail' => 'Unable to post answer.',
        'update fail' => 'Unable to update answer.',
        'delete fail' => 'Unable to delete answer.',
        'restore fail' => 'Unable to restore answer.',
    ];

    // methods

    // mutators

    // scopes

    // relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // override

    // Makeable
    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();
        
        if ($me === null) {
            $questionId = $request->question_id;
            // if there was no question, do not post
            $question = Question::find($questionId);
            if (!$question) {
                $errorMsg = static::$validationErrors['question_id.required'];
                error($errorMsg, 401);
            }

            $me = new static($data);
            user($request)->answers()->save($me);
        } else {
            // disregard even if no question
            //! NOTE: will also update question_id if specified
            $me->update($data);
        }

        // relationships

        // upload
        $me->uploadImage($request, 'img_src');

        return $me;
    }

    protected static $validationErrors = [
        'question_id.required' => 'Oops! The question was not found.',
        'question_id.exists' => 'Oops! The question was not found.',

        'content.required' => 'Content or description is required.',
        'img_src.image' => 'Uploaded item should be an image.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        // $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'question_id' => 'sometimes|required|exists:questions,id',

                'content' => 'sometimes|required',
                'img_src' => 'sometimes|image',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
