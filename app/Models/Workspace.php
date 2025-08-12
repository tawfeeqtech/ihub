<?php

namespace App\Models;

use App\Traits\JsonSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workspace extends Model
{
    use JsonSearchable;
    protected $fillable = [
        'name',
        'location',
        'bank_payment_supported',
        'bank_account_number',
        'mobile_payment_number',
        'features',
        'description',
        'logo',
        'governorate_id',
        'region_id',
        'has_evening_shift',
        'has_free',
    ];

    protected $casts = [
        'name' => 'array',
        'location' => 'array',
        'description' => 'array',
        'features' => 'array',
        'bank_payment_supported' => 'boolean',
        'has_evening_shift' => 'boolean',
        'has_free' => 'boolean',
    ];

    public $translatable = ['name', 'location', 'description'];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    // protected static function booted()
    // {
    //     static::creating(function ($workspace) {
    //         logger()->info('جاري إنشاء Workspace', request()->all());
    //     });
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
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
    public function getTranslatedNameAttribute($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $name = json_decode($this->attributes['name'], true);
        return $name[$locale] ?? $name['ar'] ?? '';
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
