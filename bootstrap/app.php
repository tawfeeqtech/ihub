<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {
        $responder = new class {
            use \App\Traits\ApiResponseTrait;
        };

        // Validation Error (422)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                return $responder->apiResponse(
                    ['errors' => $e->errors()],
                    'Validation error',
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        });

        // Unauthenticated (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                return $responder->apiResponse(
                    null,
                    'Unauthenticated',
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED
                );
            }
        });

        // Access Denied (403)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                return $responder->apiResponse(
                    null,
                    'Access Denied',
                    \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN
                );
            }
        });

        // Model Not Found (404)
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                return $responder->apiResponse(
                    null,
                    'Resource not found',
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
                );
            }
        });

        // Endpoint Not Found (404)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                return $responder->apiResponse(
                    null,
                    'Endpoint not found',
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
                );
            }
        });

        // Server Error (500)
        $exceptions->render(function (\Throwable $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                return $responder->apiResponse(
                    null,
                    'Server Error: ' . $e->getMessage(),
                    \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        });
    })
    ->withProviders([
        \L5Swagger\L5SwaggerServiceProvider::class,
    ])->create();
