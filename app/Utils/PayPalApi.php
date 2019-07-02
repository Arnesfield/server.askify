<?php

namespace App\Utils;

use App\User;
use App\Answer;
use App\Transaction as AppTransaction;

use PayPal\Api\Item;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Api\WebProfile;
use PayPal\Api\InputFields;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;

use Illuminate\Http\Request;

class PayPalApi
{
    private static $context;

    public static function getContext()
    {
        if (!static::$context) {
            $config = config('paypal');
            static::$context = new ApiContext(
                new OAuthTokenCredential(
                    $config['client_id'],
                    $config['client_secret']
                )
            );
        }

        return static::$context;
    }

    public static function getToken()
    {
        // TODO: 
    }

    protected static function makeAppTransation(Request $request, User $user, Answer $answer) {
        $aId = $answer->id;
        $aPrice = $answer->price;
        $aCurrency = $answer->currency;
    
        $aIdText = "Answer #$aId";
    
        $data = [
            // amount same as price
            'amount' => $aPrice,
            'currency' => $aCurrency,
            'description' => "Access $aIdText",
        ];

        $request->merge($data);

        return AppTransaction::makeMe($request, null, [
            'user' => $user,
            'answer_id' => $aId,
        ]);
    }

    public static function pay(Request $request, User $user, Answer $answer)
    {
        $appTrxn = static::makeAppTransation($request, $user, $answer);

        $apiContext = static::getContext();

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal('1.00')
            ->setCurrency($appTrxn->currency);

        $transaction = new Transaction();
        $transaction->setAmount($amount);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(url('pay/success'))
            ->setCancelUrl(url('pay/cancel'));

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        $error = false;
        try {
            $payment->create($apiContext);

            // update payment
            // $appTrxn->approveMe();
            $appTrxn->update([
                'payment_id' => $payment->id,
                'approval_url' => $payment->getApprovalLink(),
            ]);
        } catch (PayPalConnectionException $ex) {
            // This will print the detailed information on the exception.
            $error = $ex->getData();
        }

        // format for return lol
        $transaction = $appTrxn;
        // $payment = $payment->toArray();

        return [
            'error' => $error,
            'payment' => $payment,
            'payment_array' => $payment->toArray(),
            'transaction' => $transaction
        ];
    }

    public static function execute(Request $request)
    {
        $apiContext = static::getContext();

        $paymentId = $request->paymentId;
        $payerId = $request->PayerID;

        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);
    
        try {
            $error = false;
            $result = $payment->execute($execution, $apiContext);
        } catch (PayPalConnectionException $ex) {
            $result = false;
            $error = $ex->getData();
        }

        return compact('error', 'result');
    }

    public static function getPayment($id)
    {
        $apiContext = static::getContext();
        return Payment::get($id, $apiContext);
    }
}
