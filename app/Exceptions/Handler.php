<?php

namespace App\Exceptions;

use Exception;
use Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        // Sentry的错误捕捉
        if (env('SENTRY_ENABLE')) {
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                app('sentry')->captureException($e);
            }
        }
        // 不记录错误trace
        if($e instanceof UnprocessableEntityHttpException || $e instanceof UnauthorizedHttpException){

        }
        else {
            try {
                Log::error('['.$e->getCode().'] "'.$e->getMessage().'" on line '.$e->getTrace()[0]['line'].' of file '.$e->getTrace()[0]['file']);
            }
            catch (Exception $exception) {
                Log::error('['.$e->getCode().'] "'.$e->getMessage());
            }
        }
//        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // 404错误重定向到首页
        if($e instanceof NotFoundHttpException){
            return redirect('/' . env('QUERY_STR'));
        }
        return parent::render($request, $e);
    }
}
