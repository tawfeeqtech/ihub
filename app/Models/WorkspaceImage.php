<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceImage extends Model
{
    protected $fillable = ['workspace_id', 'image'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
