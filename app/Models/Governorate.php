<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    //
    protected $fillable = ['name'];
    protected $casts = [
        'name' => 'array',
    ];

    public array $translatable = ['name'];

    public function getTranslatedNameAttribute($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $name = json_decode($this->attributes['name'], true);
        return $name[$locale] ?? $name['ar'] ?? '';
    }


    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
