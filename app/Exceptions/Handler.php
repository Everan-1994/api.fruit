<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        InvalidRequestException::class,
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
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response|Response
     * @throws InternalException
     * @throws InvalidRequestException
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            throw new InvalidRequestException(array_first(array_collapse($exception->errors())));
        }

        if ($exception instanceof UnauthorizedHttpException) {
            throw new InvalidRequestException($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof NotFoundHttpException) {
            throw new InvalidRequestException('Sorry, the page you are looking for could not be found.', Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof TokenBlacklistedException) {
            throw new InternalException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($exception instanceof QueryException) {
            throw new InternalException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
