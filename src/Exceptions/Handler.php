<?php

namespace Antriver\LaravelSiteUtils\Exceptions;

use Auth;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Exception\NotReadableException;
use Laravel\Socialite\Two\InvalidStateException;
use Psr\Log\LoggerInterface;
use Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BadRequestHttpException::class,
        InvalidInputException::class,
        NotReadableException::class,
        InvalidStateException::class,
    ];

    protected function shouldRenderAsJson(Request $request): bool
    {
        return $request->wantsJson() || $request->isJson();
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        $isJson = $this->shouldRenderAsJson($request);

        if (!$isJson && $exception instanceof MaintenanceModeException) {
            return response()->view(
                'errors.503',
                [
                    'exception' => $exception,
                ],
                503
            );
        } /*elseif (!$isJson && $exception instanceof NotFoundHttpException) {
            return response()->view(
                'errors.404',
                [
                    'exception' => $exception,
                ],
                404
            );
        }*/ elseif (!$isJson && $exception instanceof InvalidStateException) {
            return response()->redirectTo('/');
        }

        if (config('app.debug') && !$isJson) {
            return $this->convertExceptionToResponse($exception);
        }

        $data = $this->getDataForException($exception, $request);

        /*$data['request'] = [
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'query' => $request->query->all(),
            'request' => $request->request->all(),
        ];*/

        if ($isJson) {
            return Response::json($data, $data['status']);
        }

        if ($exception instanceof ValidationException) {
            // Laravel's ValidationExceptions are recoverable - redirect the user back to the form with errors.
            // The API throws InvalidInputExceptions, with an array of messages, instead of ValidationExceptions.
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        $data['currentUser'] = Auth::user();
        $data['activeNavItem'] = null;
        $data['activeSubnav'] = null;
        $data['activeSubnavItem'] = null;

        $data['isApp'] = request()->getHost() === config('app.app_domain');
        $data['isWeb'] = request()->getHost() === config('app.web_domain');

        return response()->view('errors.general', $data, $data['status']);
    }

    /**
     * @param Exception $exception
     *
     * @param Request|null $request
     *
     * @return array
     */
    public function getDataForException(Exception $exception, Request $request = null)
    {
        if ($exception instanceof ValidationException) {
            $status = 400;
        } elseif ($exception instanceof ModelNotFoundException) {
            $status = 404;
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
        } else {
            $status = 500;
        }

        $exceptionClass = explode('\\', get_class($exception));
        $exceptionClass = array_pop($exceptionClass);

        $statusTexts = \Symfony\Component\HttpFoundation\Response::$statusTexts;
        $statusText = isset($statusTexts[$status]) ? $statusTexts[$status] : null;

        $data = $this->convertExceptionToArray($exception);
        unset(
            $data['exception'],
            $data['message']
        );

        $data['type'] = $exceptionClass;
        $data['status'] = $status;
        $data['statusText'] = $statusText;

        if ($exception instanceof ValidationException) {
            $errors = [];
            foreach ($exception->errors() as $key => $messages) {
                $errors = array_merge($errors, $messages);
            }
            $data['errors'] = $exception->errors();
            $data['error'] = trim(implode(' ', $errors));
        } elseif ($exception instanceof InvalidInputException) {
            $data['errors'] = $exception->getMessages();
            $data['error'] = $exception->getMessage();
        } elseif ($exception instanceof QueryException) {
            $data['error'] = 'There was a problem with the '.config('app.name').' database.';
            $data['additionalHtml'] = 'Please try again or contact us if you keep seeing this message.';
            if (config('app.debug') === true || ($request->user() && $request->user()->moderator)) {
                $data['additionalHtml'] .= '<br/><small>'.$exception->getMessage().'</small>';
            } else {
                $data['additionalHtml'] .= '<br/><small>'.$exception->getPrevious()->getMessage().'</small>';
            }
        } else {
            $data['error'] = $exception->getMessage();
        }

        if (method_exists($exception, 'getData')) {
            $data = array_merge(
                $data,
                $exception->getData()
            );
        }

        // Ensure there is always an error message.
        if (empty($data['error'])) {
            $data['error'] = 'There was a problem with your request ('.$data['type'].')';
        }

        // If 'trace' exists in the response move it to the end of the array for readability.
        if (array_key_exists('trace', $data)) {
            $trace = $data['trace'];
            unset($data['trace']);
            //$data['trace'] = $trace;
        }

        return $data;
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        try {
            return array_filter(
                [
                    'User ID' => Auth::id(),
                    'Username' => Auth::user() ? Auth::user()->username : null,
                ]
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Report or log an exception.
     *
     * @param \Exception $e
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return null;
        }

        if (method_exists($e, 'report')) {
            return $e->report();
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e;
        }

        $request = app(\Illuminate\Http\Request::class);

        $logger->error(
            $e->getMessage(),
            array_merge(
                $this->context(),
                [
                    'URL' => \Request::fullUrl(),
                    'Class' => get_class($e),
                    'File' => $e->getFile(),
                    'Line' => $e->getLine(),
                    'Exception' => $e,
                    'Hostname' => gethostname(),
                    'Backtrace' => $e->getTraceAsString(),
                    'Client IP' => $request->getClientIp(),
                    'User Agent' => $request->userAgent(),
                    'User' => $request->user() ? $request->user()->username : null,
                    'Referrer' => $request->server('HTTP_REFERER'),
                ]
            )
        );
    }
}
