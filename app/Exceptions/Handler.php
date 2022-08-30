<?php

/*
 * CODE
 * Handler Class
 */

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;
use Exception;
use InvalidArgumentException;

/**
 * @access  public
 *
 * @version 1.0
 */
class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport
        = [
            //
        ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash
        = [
            'current_password',
            'password',
            'password_confirmation',
        ];

    /**
     * @param Exception|Throwable $e
     *
     * @throws Throwable
     */
    public function report(Exception|Throwable $e)
    {
        parent::report($e);
    }

    /**
     * @param Request $request
     * @param Exception|Throwable $e
     *
     * @return Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render(
        $request,
        Exception|Throwable $e
    ): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));

            return $this->errorResponse(
                "No existe ninguna instancia de {$model} con el id especificado",
                404
            );
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof AuthorizationException) {
            return $this->errorResponse(
                'No posee permisos para ejecutar esta acción',
                403
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse(
                'No se encontró la URL especificada',
                404
            );
        }

        if ($e instanceof MethodNotAllowedException) {
            return $this->errorResponse(
                'El método especificado en la petición no es válido',
                405
            );
        }

        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getSattusCode());
        }

//        if ($e instanceof QueryException) {
//            return $this->errorResponse($e->getMessage(), 500);

//            if (1451 === $code) {
//                return $this->errorResponse(
//                    'No se puede elminar de forma permanente el recurso por que esta relacionado con otro',
//                    409
//                );
//            }
//        }

//        if ($e instanceof InvalidArgumentException) {
//            return $this->errorResponse($e->getMessage(), $e->getCode());
//            return $this->errorResponse('No autenticado.', 401);
//        }

        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return $this->errorResponse('Falla inesperada. Intente Despues', 500);
    }


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
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param Request $request
     * @param AuthenticationException $e
     *
     * @return JsonResponse
     */
    protected function unauthenticated(
        $request,
        AuthenticationException $e
    ): JsonResponse
    {
        return $this->errorResponse('No autenticado.', 401);
    }

    /**
     * @param ValidationException $e
     * @param Request $request
     *
     * @return Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(
        ValidationException $e,
                            $request
    ): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
    }
}
