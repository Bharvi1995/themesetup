<?php
namespace App\Exceptions;

use Mail;
use Exception;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use App\Mail\ExceptionOccured;

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
        if ($this->shouldReport($exception)) {
            $this->sendEmail($exception); // sends an email
        }
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
        // if ($exception instanceof UnauthorizedException) {
        //     if(\Auth::user() instanceof Admin) {
        //         return redirect()->route('dashboard');
        //     }
        //     return redirect()->route('dashboard');
        // }
        // if ($request->wantsJson() || $request->expectsJson() || $request->isJson()) { 
        //     return $this->handleApiException($request, $exception);
        // } else {
        //     $retval = parent::render($request, $exception);
        // }
        // return $retval;
        return parent::render($request, $exception);
    }

    private function handleApiException($request, Exception $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = 'Unauthorized';
                break;
            case 403:
                $response['message'] = 'Forbidden';
                break;
            case 404:
                $response['message'] = 'Not Found';
                break;
            case 405:
                $response['message'] = 'Method Not Allowed';
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
                break;
        }

        if (config('app.debug')) {
            $response['trace'] = $exception->getTrace();
            $response['code'] = $exception->getCode();
        }

        $response['status'] = $statusCode;

        return response()->json($response, $statusCode);
    }

    public function sendEmail(Throwable $exception)
    {
        try {
            $isException  = $exception instanceof Exception;
            if ($isException === false) {
                $exception = new Exception($exception);
            }

            $e = FlattenException::create($exception);

            $handler = new HtmlErrorRenderer(true);
            $css = $handler->getStylesheet();

            $content = $handler->getBody($e);
            if (Str::contains($exception->getMessage(), 'Deadlock found when trying to get lock')) {
                // 
            } elseif (Str::contains($exception->getMessage(), 'Lock wait timeout exceeded')) {
                // 
            } elseif (env('SEND_ERROR_MAIL')) {
                Mail::to('test@gmail.com')
                    ->cc(['test@gmail.com'])
                    ->send(new ExceptionOccured($content, $css));
            }            
        } catch (Throwable $ex) {
            \Log::info(['error_mail_report' => $ex->getMessage()]);
            // dd($ex);
        }
    }
}
