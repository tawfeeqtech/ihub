<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\Booking;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($bookingId);
            $sender = auth()->user(); // App user
            if ($booking->user_id !== $sender->id) {
                return $this->apiResponse(null, __('messages.not_authorized'), 403);
            }

            if ($booking->status !== 'confirmed') {
                return $this->apiResponse(null, __('messages.confirmed_request'), 422);
            }

            $validated = $request->validate([
                'type' => 'required|in:seat_change,cafe_request',
                'details' => 'nullable|string|max:1000',
            ]);

            $recipient = User::find($booking->workspace->secretary->id); // Secretary
            // التحقق من التكرار
            $existing = ServiceRequest::where('user_id', $sender->id)
                ->where('booking_id', $bookingId)
                ->where('type', $validated['type'])
                ->whereIn('status', ['pending', 'in_progress'])
                ->first();

            if ($existing) {
                return $this->apiResponse(null, __('messages.already_request'), 422);
            }

            // الإنشاء
            $serviceRequest = ServiceRequest::create([
                'user_id' => $sender->id,
                'booking_id' => $bookingId,
                'type' => $validated['type'],
                'details' => $validated['details'],
                'status' => 'pending',
            ]);


            // Send notification to the secretary
            $notificationService = app(NotificationService::class);
            $notificationService->sendServiceRequestNotification(
                $recipient, // Secretary
                $sender,    // App user
                $validated['type'],
                true        // Secretary notification
            );

            DB::commit();

            return $this->apiResponse(new ServiceRequestResource($serviceRequest), __('messages.success'), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
