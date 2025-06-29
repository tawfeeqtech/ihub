<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // أضف هذا
use Illuminate\Support\Facades\App;

class SyncUserLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && $request->hasSession()) {
            $user = Auth::user();
            $currentLocale = app()->getLocale(); // Get the locale set by kenepa/translation-manager
            Log::info('currentLocale: ' . $currentLocale . ', User locale: ' . $user->locale); // سجل حالة المستخدم واللغة

            if ($user->locale !== $currentLocale) {
                $user->update(['locale' => $currentLocale]); // Sync user's locale with session
                Log::info('user->locale !== $currentLocale  ' . $currentLocale . ' for user ID: ' . $user->id); // سجل التحديث

            } else {
                Log::info('error user->locale === $currentLocale ' . $currentLocale . '. No update needed.'); // سجل عدم الحاجة للتحديث
            }
        } else {
            Log::info('UpdateUserLocale Middleware: User is not authenticated.'); // سجل عدم تسجيل الدخول
        }
        Log::info('UpdateUserLocale Middleware: Finished.'); // سجل نهاية الـ Middleware

        return $next($request);
    }
}
