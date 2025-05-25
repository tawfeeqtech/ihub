<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait JsonSearchable
{
    public function scopeSearchJsonField(Builder $query, string $field, string $term, array $langs = ['en', 'ar']): Builder
    {
        $cleaned = strtolower(str_replace(['-', ' '], '', $term));

        return $query->where(function ($q) use ($field, $langs, $cleaned) {
            foreach ($langs as $lang) {
                $q->orWhereRaw("
                    LOWER(REPLACE(REPLACE(JSON_UNQUOTE(JSON_EXTRACT($field, '$.\"$lang\"')), '-', ''), ' ', '')) LIKE ?
                ", ["%$cleaned%"]);
            }
        });
    }
}
