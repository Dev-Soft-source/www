<?php

namespace App\Exceptions;

use App\View\Composers\ErrorPageComposer;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
        'current_password',
        // 'password',
        // 'password_confirmation',
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

    /**
     * Render an exception into an HTTP response. Pass errorPage to 404 view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundHttpException || ($e instanceof HttpException && $e->getStatusCode() === 404)) {
            $this->setLocaleFromRequest($request);
            $errorPage = ErrorPageComposer::getErrorPage();
            return response()->view('errors.404', ['errorPage' => $errorPage], 404);
        }

        return parent::render($request, $e);
    }

    /**
     * Set app locale from first URL segment (e.g. /zh/...) so 404 page uses correct language.
     */
    protected function setLocaleFromRequest($request): void
    {
        $segment = $request->segment(1);
        if ($segment && strlen($segment) === 2) {
            app()->setLocale($segment);
            session(['selectedLanguage' => $segment]);
        }
    }
}
