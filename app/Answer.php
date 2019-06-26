<?php

namespace App;

use App\Utils\FileUploadable\FileUploadable;
use App\Utils\FileUploadable\FileUploadableContract;
use App\Utils\Voteable\Voteable;
use App\Utils\Voteable\VoteableContract;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer
    extends CommonModel
    implements FileUploadableContract, VoteableContract
{
    use FileUploadable, Voteable;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'question_id',
        'content', 'img_src', 'price', 'currency',
        'deleted_at', 'privated_at'
    ];

    protected $appends = [
        'votes_total',
    ];

    protected $dates = [
        'deleted_at', 'privated_at',
    ];

    protected $attributes = [
        'user_id' => null,
        'question_id' => null,
        'content' => '',
        'img_src' => null,
        'price' => 0,
        'currency' => 'USD',
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

    public function scopePublic($query)
    {
        return $query->whereNull('privated_at');
    }

    // relationships

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function votes()
    {
        return $this->morphMany('App\Vote', 'voteable');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    // override

    // Makeable
    protected static function validateOnCreate($data) {
        // if there was no question, do not post
        $R = static::getValidationRules();
        $validator = \Validator::make(
            $data,
            [ 'question_id' => 'required|exists:questions,id' ],
            $R['errors']
        );

        if ($validator->fails()) {
            $errorMsg = $R['errors']['question_id.required'];
            error($errorMsg, 400);
        }
    }

    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();
        if ($request->get('make_private')) {
            $data['privated_at'] = nowDt();
        }
        
        if ($me === null) {
            $user = user($request);
            static::validateOnCreate($data);

            $me = new static($data);
            $user->answers()->save($me);
        } else {
            // disregard even if no question
            //! NOTE: will also update question_id if specified
            $me->update($data);
        }

        // relationships

        // upload
        $me->uploadImage($request, 'file_img_src', 'img_src', 'answers/');

        return $me;
    }

    protected static $validationErrors = [
        'question_id.required' => 'Oops! The question was not found.',
        'question_id.exists' => 'Oops! The question was not found.',

        'content.required' => 'Content or description is required.',
        // 'img_src.image' => 'Uploaded item should be an image.',
        'price.required_with' => 'Price is required.',
        'price.numeric' => 'Invalid price.',
        'price.min' => 'Price should be 0 or more.',
        'price.max' => 'Price should not exceed 10.',
        'currency.required' => 'Currency is required.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        // $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'question_id' => 'sometimes|required|exists:questions,id',

                'content' => 'sometimes|required',
                // 'img_src' => 'sometimes|image',
                'price' => 'required_with:make_private|numeric|min:0|max:10',
                'currency' => 'sometimes|required',
                'make_private' => 'sometimes|required'
            ],
            'errors' => static::$validationErrors
        ];
    }
}
