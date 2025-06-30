<?php

namespace App\Models;

use App\ServiceRequestsStatus;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'type',
        'details',
        'status',
    ];

    protected $casts = [
        'status' => ServiceRequestsStatus::class,
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'id', 'booking');
    }

    public function scopeForWorkspace($query, int $workspaceId)
    {
        return $query->whereHas('booking', function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        });
    }
}
