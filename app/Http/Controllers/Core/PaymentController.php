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

        if ($payment = $res['payment']) {
            return redirect($payment->getApprovalLink());
        }

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

        $msg = $res ? 'Transaction successful.' : 'Unable to approve transaction.';

        // goto client
        return $this->gotoClient($msg);

        // disregard
        return $res
            ? jresponse($msg)
            : jresponse($msg, 400);
    }

    public function cancel(Request $request)
    {
        // $token = $payment->token;
        $msg = 'Transaction cancelled.';
        return $this->gotoClient($msg);

        // disregard
        return jresponse($msg);
    }

    protected function gotoClient($msg = '') {
        $params = $msg ? "?msg=$msg" : '';
        $url = env('APP_CLIENT_URL') . $params;

        return redirect($url);
    }
}
