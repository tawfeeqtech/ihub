<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // أضف هذا

class LanguageController extends Controller
{
    public function changeLanguage($locale)
    {
        // تحقق إن اللغة مدعومة
        if (! in_array($locale, ['en', 'ar'])) {
            abort(400, 'Unsupported language');
        }
        // تغيير اللغة في الجلسة
        App::setLocale($locale);
        Session::put('language', $locale);
        // تحديث اللغة في قاعدة البيانات إذا المستخدم مسجل دخوله
        if (Auth::check()) {
            $user = Auth::user();
            // Log::info('before: ' . $user->locale);
            $user->locale = $locale;
            // Log::info('after:' . $user->locale);
            $user->save();
        }

        return redirect()->back();
    }
}
