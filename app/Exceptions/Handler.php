<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->isJson() && $exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Token',
            ], 500);
        }

        if ($request->isJson() && $exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Token',
            ], 500);
        }

        if ($request->isJson() && $exception instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'message' => 'Token Expired',
            ], 500);
        }

        if ($request->isJson() && $exception instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'message' => 'Token Invalid',
            ], 500);
        }
        return parent::render($request, $exception);
    }
}
