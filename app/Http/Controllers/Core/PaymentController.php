<?php

namespace App\Http\Controllers\Core;

use App\Answer;
use App\Transaction;
use App\Utils\PayPalApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PayPal\Api\PaymentExecution;

class PaymentController extends Controller
{

    public function pay(Request $request, $id)
    {
        $answer = Answer::find($id);
        $res = PayPalApi::pay($request, user($request), $answer);
        return !$res['error']
            ? jresponse($res)
            : jresponse($res, 400);
    }

    public function show(Request $request, $id)
    {
        $payment = PayPalApi::getPayment($id);
        $payment = $payment->toArray();
        return jresponse($payment);
    }

    public function success(Request $request)
    {
        $paymentId = $request->paymentId;
        // $token = $request->token;
        // $PayerID = $request->PayerID;

        // find in transactions using paymentId
        $transaction = Transaction::where('payment_id', $paymentId)->first();
        if (!$transaction) {
            return jresponse('Transaction not found.', 404);
        }

        // execute payment
        $execRes = PayPalApi::execute($request);
        $res = $execRes['result'] && $transaction->approveMe();

        return $res
            ? jresponse('Transaction successful.')
            : jresponse('Unable to approve transaction.', 400);
    }

    public function cancel(Request $request)
    {
        // $token = $payment->token;
        return jresponse('Transaction cancelled.');
    }
}
