<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        $locale = null;

        // 1. الأولوية الأولى: التحقق من وجود لغة في الـ Session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        elseif ($request->user()) {
            $locale = $request->user()->locale;

        }

        // إذا لم يتم تحديد لغة، استخدم اللغة الافتراضية من ملف الإعدادات
        $locale = $locale ?: config('app.locale');

        // تحقق من أن اللغة مدعومة قبل تعيينها
        if (in_array($locale, config('app.available_locales', ['en', 'ar']))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
