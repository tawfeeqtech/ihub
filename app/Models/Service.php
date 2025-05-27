<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'workspace_id',
        'category',
        'name',
    ];

    protected $casts = [
        'name' => 'array',
        'category' => 'array',
    ];

    public array $translatable = ['name', 'category'];
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
