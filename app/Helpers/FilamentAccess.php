<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class FilamentAccess
{
    public static function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function isSecretary(): bool
    {
        return Auth::user()?->role === 'secretary';
    }
}
