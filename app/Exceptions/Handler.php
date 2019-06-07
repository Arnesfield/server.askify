<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,

        AppException::class,
        NotAnInstanceOfException::class,
        NotAuthException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        // check for exceptions
        if ($exception instanceof AppException) {
            return jresponse($exception->getMessage(), $exception->getStatus());
        } else if ($exception instanceof NotAuthException) {
            return jresponse('You must be logged in first.', 401);
        } else if ($exception instanceof ValidationException) {
            // only return the first message ;)
            $errors = $exception->validator->errors()->all();
            $msg = $exception->getMessage();

            // get first string message only
            $res = $errors[0] ?? $msg;

            return jresponse($res, $exception->status);
        }

        return parent::render($request, $exception);
    }
}
