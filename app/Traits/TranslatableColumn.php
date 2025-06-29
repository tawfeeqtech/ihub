<?php

namespace App\Traits;

trait TranslatableColumn
{
    public static function resolveTranslatedField($state)
    {
        $locale = auth()->user()?->locale ?? app()->getLocale();
        $decoded = is_string($state) ? json_decode($state, true) : $state;

        return is_array($decoded) && isset($decoded[$locale])
            ? $decoded[$locale]
            : (is_array($decoded) ? collect($decoded)->first() : $state);
    }
}
