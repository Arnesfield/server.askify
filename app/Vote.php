<?php

namespace App;

use App\Question;
use App\Answer;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Vote extends CommonModel implements FileUploadableContract
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'upvoted_at', 'downvoted_at',
        'deleted_at',
    ];

    protected $dates = [
        'upvoted_at', 'downvoted_at',
        'deleted_at',
    ];

    protected $attributes = [
        'user_id' => null,
        'upvoted_at' => null,
        'downvoted_at' => null,
		'deleted_at' => null,
    ];

    protected static $responseMessages = [
        'not found' => 'Vote not found.',

        'create success' => 'Vote casted.',
        'update success' => 'Vote updated.',
        'delete success' => 'Vote deleted.',
        'restore success' => 'Vote restored.',

        'create fail' => 'Unable to cast vote.',
        'update fail' => 'Unable to update vote.',
        'delete fail' => 'Unable to delete vote.',
        'restore fail' => 'Unable to restore vote.',
    ];

    // methods
    public function vote($args)
    {
        $now = nowDt();
        $res = [
            'upvoted_at' => null,
            'downvoted_at' => null,
        ];

        if ($args['unvote'] ?? false) {
            return $this->delete();
        } else {
            if ($args['upvote'] ?? false) {
                $this->restore();
                $res['upvoted_at'] = $now;
            } else if ($args['downvote'] ?? false) {
                $this->restore();
                $res['downvoted_at'] = $now;
            } else {
                // if nothin, don't proceed so as to not null all vote cols
                return null;
            }
        }

        return $this->update($res);
    }

    // mutators

    // scopes

    // relationships

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function voteable()
    {
        return $this->morphTo();
    }

    // override

    // Makeable
    protected static function validateOnCreate($data) {
        $R = static::getValidationRules();
        $validator = \Validator::make($data, $R['rules'], $R['errors']);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected static function getVoteable(Request $request) {
        // get $voteable (either question or answer *badumtsss)
        $voteables = $request->only(['question_id', 'answer_id']);
        $qId = $voteables['question_id'] ?? false;
        $aId = $voteables['answer_id'] ?? false;

        //! assert that $voteable will have a value
        return $qId
            ? Question::find($qId)
            : ( $aId ? Answer::find($aId) : null );
    }

    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();

        if ($me === null) {
            $user = user($request);
            static::validateOnCreate($data);

            $voteable = static::getVoteable($request);

            $me = new static($data);
            $me->user()->associate($user);
            $me->voteable()->associate($voteable);
            $me->save();
        } else {
            $me->update($data);
        }

        $me->vote( $request->only(['upvote', 'downvote', 'unvote']) );
        
        return $me;
    }

    protected static $validationErrors = [
        'auto.required_without_all' => 'Oops! Unable to save vote.',

        'question_id.required_without_all' => 'Oops! The question was not found.',
        'question_id.exists' => 'Oops! The question was not found.',

        'answer_id.required_without_all' => 'Oops! The answer was not found.',
        'answer_id.exists' => 'Oops! The answer was not found.',

        'upvote.required_without_all' => 'Unable to upvote.',
        'downvote.required_without_all' => 'Unable to downvote.',
        'unvote.required_without_all' => 'Unable to remove vote.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        return [
            'rules' => [
                'auto' => 'required_without_all:question_id,answer_id',
                'question_id' => 'required_without_all:auto,answer_id|exists:questions,id',
                'answer_id' => 'required_without_all:auto,question_id|exists:answers,id',

                'upvote' => 'required_without_all:downvote,unvote',
                'downvote' => 'required_without_all:upvote,unvote',
                'unvote' => 'required_without_all:upvote,downvote',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
