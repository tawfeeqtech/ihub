<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'price',
        'duration',
    ];

    // تعريف علاقة مع Workspace
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
