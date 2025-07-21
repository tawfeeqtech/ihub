<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Region extends Model
{
    protected $fillable = ['name', 'governorate_id'];
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

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }
}
