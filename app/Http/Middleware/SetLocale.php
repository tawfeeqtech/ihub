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
        // $locale = Session::get('locale', config('app.locale'));
        // if (in_array($locale, config('app.available_locales', ['en']))) {
        //     App::setLocale($locale);
        // }

        $locale = $request->user() ? $request->user()->locale : Session::get('locale', config('app.locale'));
        if (in_array($locale, config('app.available_locales', ['en']))) {
            App::setLocale($locale);
            Log::info('SetLocale: ' . $locale);
        }

        return $next($request);
    }
}
