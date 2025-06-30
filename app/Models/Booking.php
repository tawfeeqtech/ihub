<?php

namespace App\Models;

use App\BookingStatus;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id',
        'package_id',
        'seat_number',
        'wifi_username',
        'wifi_password',
        'start_at',
        'end_at',
        'status',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
