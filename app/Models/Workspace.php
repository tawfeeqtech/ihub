<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = [
        'name',
        'location',
        'bank_account_number',
        'mobile_payment_number',
        'features',
        'description',
        'logo',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function images()
    {
        return $this->hasMany(WorkspaceImage::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function secretary()
    {
        return $this->hasOne(User::class, 'workspace_id', 'id')->where('role', 'secretary');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
