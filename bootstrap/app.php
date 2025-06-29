<?php

use App\Http\Middleware\SetAppLocale;
use App\Http\Middleware\SyncUserLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Broadcasting\BroadcastServiceProvider;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        api: [
            __DIR__ . '/../routes/api_v1.php',
            // __DIR__ . '/../routes/api_v2.php',
        ],
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SetAppLocaleFromHeader::class);
        // $middleware->web([\App\Http\Middleware\SyncUserLocaleWithFilament::class]);
        // $middleware->web(append: [
        //     SetAppLocale::class,
        //     SyncUserLocale::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $responder = new class {
            use \App\Traits\ApiResponseTrait;
        };

        // Validation Error (422)
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($responder) {
            if ($request->expectsJson()) {
                // استخراج كل رسائل الخطأ ودمجها في جملة واحدة
                $fields = array_keys($e->errors());
                // تحويل الحقول لأسماء مفهومة
                $translatedFields = collect($fields)->map(function ($field) {
                    return match ($field) {
                        // 'phone' => 'الهاتف',
                        // 'password' => 'كلمة المرور',
                        // أضف باقي الحقول هنا إذا احتجت
                        default => $field,
                    };
                })->implode('، ');

                // الرسالة النهائية
                $message = $translatedFields . " error";
                return $responder->apiResponse(
                    null,
                    $message,
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
        BroadcastServiceProvider::class,
    ])->create();
