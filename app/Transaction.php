<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends CommonModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'answer_id',
        'amount', 'currency', 'payment_id', 'description',
        'approval_url',
        'deleted_at', 'approved_at',
    ];

    protected $dates = [
        'deleted_at', 'approved_at',
    ];

    protected $attributes = [
        'user_id' => null,
		'answer_id' => null,
        'amount' => 0,
		'currency' => 'USD',
		'payment_id' => null,
		'description' => null,
		'approval_url' => null,
		'approved_at' => null,
		'deleted_at' => null,
    ];

    protected static $responseMessages = [
        'not found' => 'Transaction not found.',

        'create success' => 'Transaction created.',
        'update success' => 'Transaction updated.',
        'delete success' => 'Transaction deleted.',
        'restore success' => 'Transaction restored.',

        'create fail' => 'Unable to create transaction.',
        'update fail' => 'Unable to update transaction.',
        'delete fail' => 'Unable to delete transaction.',
        'restore fail' => 'Unable to restore transaction.',
    ];

    // methods

    public function approveMe($approve = true)
    {
        return $this->update([
            'approved_at' => $approve ? nowDt() : null
        ]);
    }

    // mutators

    // scopes

    // relationships

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answer()
    {
        return $this->belongsTo('App\Answer');
    }

    // override

    // Makeable
    public static function makeMe(Request $request, $me = null, $meta = [])
    {
        $data = $request->all();
        
        if ($me === null) {
            // check meta for user to avoid multiple queries
            $user = $meta['user'] ?? user($request);
            $data['answer_id'] = $meta['answer_id'] ?? $request->route('id');

            $me = new static($data);
            $me->user()->associate($user);
            $me->save();
        } else {
            $me->update($data);
        }

        return $me;
    }

    protected static $validationErrors = [
        'answer_id.required' => 'Oops! The answer was not found.',
        'answer_id.exists' => 'Oops! The answer was not found.',

        'amount.required' => 'Amount is required.',
        'amount.min' => 'Minimum amount is 0.',
        'currency.required' => 'Currency is required.',
        'payment_id.required' => 'Invoice Number is required.',
        'payment_id.unique' => 'Invoice Number is already taken. Try again.',
    ];

    // Validateable
    public static function getValidationRules($id = null, $meta = [])
    {
        $idCond = $id ? ",$id" : '';

        return [
            'rules' => [
                'answer_id' => 'sometimes|required|exists:answer,id',

                'amount' => 'sometimes|required|numeric|min:0',
                'currency' => 'sometimes|required',
                'payment_id' => 'sometimes|required|unique:transactions,payment_id',
                'description' => 'sometimes',
                'approval_url' => 'sometimes',
            ],
            'errors' => static::$validationErrors
        ];
    }
}
