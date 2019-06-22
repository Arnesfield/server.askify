<?php

namespace App\Http\Controllers\Core;

use App\Answer;
use App\Transaction;
use App\Utils\PaypalApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PayPal\Api\PaymentExecution;

class PaymentController extends Controller
{

    public function index(Request $request, $id)
    {
        return view('paypal');
    }

    public function pay(Request $request, $id)
    {
        $answer = Answer::find($id);
        $res = PaypalApi::pay($request, user($request), $answer);
        return !$res['error']
            ? jresponse($res)
            : jresponse($res, 400);
    }

    public function show(Request $request, $id)
    {
        $payment = PaypalApi::getPayment($id);
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
        $execRes = PaypalApi::execute($request);
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
