<?php

namespace App\Models;

use App\Traits\JsonSearchable;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use JsonSearchable;
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
        'name' => 'array',
        'location' => 'array',
        'description' => 'array',
        'features' => 'array',
    ];

    public $translatable = ['name', 'location', 'description'];

    protected static function booted()
    {
        static::creating(function ($workspace) {
            logger()->info('جاري إنشاء Workspace', request()->all());
        });
    }


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
