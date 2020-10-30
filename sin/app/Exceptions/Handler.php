<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
     * @return \Illuminate\Http\Response
     */
    // public function render($request, Exception $exception)
    // {
    //   // aggiunta Davide per authorie() method in the controller launch this Exception if the user ha no the authorization
    //   // if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
    //   //     return response()->view('errors.401');
    //   // }
    //   //
    //   // if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
    //   //   // Code here ...
    //   //   return response()->view('errors.401');
    //   // }

    //     return parent::render($request, $exception);


    // }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'))->withError("Operazione non permessa da utente ospite.");
        // return redirect()->guest(route('auth.guest'));
    }
    
    public function render($request, Exception $e)
    {
        $this->registerErrorViewPaths();

        if ($e instanceof \Illuminate\Session\TokenMismatchException)
        {
            return redirect()
                    ->back()
                    ->withInput($request->except('password'))
                    ->with([
                        'status' => 'Oops! Your Validation Token has expired. Please try again',
                        'alert' => 'danger']);
        }

        return parent::render($request, $e);
    }

}
