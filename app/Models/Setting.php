<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    protected $casts = [
        'value' => 'array',
    ];
    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($value)) {
            $decoded = json_decode(stripslashes($value), true);
            $this->attributes['value'] = json_encode($decoded ?? $value, JSON_UNESCAPED_UNICODE);
        }
    }

    public function getTranslatedNameAttribute($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->value;

        if (is_string($value)) {
            $value = json_decode(stripslashes($value), true) ?? [];
        }

        $firstItem = collect($value['info'] ?? [])->first();
        return data_get($firstItem, "value.{$locale}", data_get($firstItem, 'value.en', 'غير متوفر'));
    }

    public function getItemCountAttribute()
    {
        $value = $this->value;
        if (is_string($value)) {
            $value = json_decode(stripslashes($value), true) ?? [];
        }
        return count($value['info'] ?? []);
    }

    public function getContactsAttribute()
    {
        $value = $this->value;
        if (is_string($value)) {
            $value = json_decode(stripslashes($value), true) ?? [];
        }
        return $value['contacts'] ?? null;
    }
    public function getLinksAttribute()
    {
        $value = $this->value;
        if (is_string($value)) {
            $value = json_decode(stripslashes($value), true) ?? [];
        }
        return $value['links'] ?? null;
    }
    // public function getTranslatedDescriptionAttribute()
    // {
    //     $value = $this->value;

    //     if (is_string($value)) {
    //         $decoded = json_decode($value, true);
    //         if (json_last_error() === JSON_ERROR_NONE) {
    //             $value = $decoded;
    //         } else {
    //             $value = json_decode(stripslashes($value), true) ?? [];
    //         }
    //     }

    //     $firstItem = collect($value)->first();
    //     return data_get($firstItem, 'value.ar', data_get($firstItem, 'value.en', 'غير متوفر'));
    // }
    // public function setValueAttribute($value)
    // {
    //     if (is_array($value)) {
    //         $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    //     } elseif (is_string($value)) {
    //         $this->attributes['value'] = json_encode(json_decode($value, true) ?? $value, JSON_UNESCAPED_UNICODE);
    //     }
    // }
    // public function getTranslatedNameAttribute($locale = null)
    // {
    //     $locale = $locale ?? app()->getLocale();
    //     $value = is_string($this->value) ? json_decode($this->value, true) : $this->value;
    //     $firstItem = collect($value)->first();
    //     return data_get($firstItem, "value.{$locale}", 'غير متوفر');
    // }

    // public function setValueAttribute($value)
    // {
    //     if (is_array($value)) {
    //         $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    //     } elseif (is_string($value)) {
    //         $decoded = json_decode(stripslashes($value), true);
    //         $this->attributes['value'] = json_encode($decoded ?? $value, JSON_UNESCAPED_UNICODE);
    //     }
    // }
    // public function getTranslatedNameAttribute($locale = null)
    // {
    //     $locale = $locale ?? app()->getLocale();
    //     $value = $this->value;

    //     if (is_string($value)) {
    //         $value = json_decode(stripslashes($value), true) ?? [];
    //     }

    //     $firstItem = collect($value)->first();
    //     return data_get($firstItem, "value.{$locale}", data_get($firstItem, 'value.en', 'غير متوفر'));
    // }

    // // accessor إضافي لعدد العناصر (اختياري)
    // public function getItemCountAttribute()
    // {
    //     $value = $this->value;
    //     if (is_string($value)) {
    //         $value = json_decode(stripslashes($value), true) ?? [];
    //     }
    //     return count($value);
    // }


}
