<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\Booking;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    use ApiResponseTrait;

    // عرض طلبات الخدمات الخاصة بالمستخدم
    public function index($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }

        return $this->apiResponse(ServiceRequestResource::collection(
            $booking->serviceRequests()->latest()->get()
        ), __('messages.success'), 200);
    }

    // إنشاء طلب خدمة جديد
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }

        if ($booking->status !== 'confirmed') {
            return $this->apiResponse(null, __('messages.confirmed_request'), 422);
        }

        $validated = $request->validate([
            'type' => 'required|in:seat_change,cafe_request',
            'details' => 'nullable|string|max:1000',
        ]);

        // التحقق من التكرار
        $existing = ServiceRequest::where('user_id', auth()->id())
            ->where('booking_id', $bookingId)
            ->where('type', $validated['type'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if ($existing) {
            return $this->apiResponse(null, __('messages.already_request'), 422);
        }

        // الإنشاء
        $serviceRequest = ServiceRequest::create([
            'user_id' => auth()->id(),
            'booking_id' => $bookingId,
            'type' => $validated['type'],
            'details' => $validated['details'],
            'status' => 'pending',
        ]);

        return $this->apiResponse(new ServiceRequestResource($serviceRequest), __('messages.success'), 200);
    }
}
