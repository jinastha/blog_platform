<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof RouteNotFoundException) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        if ($exception instanceof AuthenticationException || $exception instanceof UnauthorizedHttpException || $exception instanceof TokenExpiredException || $exception instanceof TokenInvalidException || $exception instanceof JWTException) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => $exception->getMessage()
            ], 401);
        }

        if (config('app.debug')) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ], 500);
        }
        return parent::render($request, $exception);
    }
}
