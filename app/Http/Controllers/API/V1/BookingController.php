<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Http\Resources\BookingResource;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\NewBookingNotification;
use App\Services\NotificationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Booking::where('user_id', auth()->id());

        if ($status === 'active') {
            $query->where('status', 'confirmed');
        } elseif ($status === 'history') {
            $query->where(function ($q) {
                $q->where('status', 'cancelled')
                    ->orWhere('end_at', '<', now());
            });
        }

        $bookings = $query->latest()->get();
        return $this->apiResponse(BookingResource::collection($bookings), __('messages.success'), 200);
    }

    public function show($id)
    {
        try {
            $booking = Booking::with(['workspace', 'package'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->apiResponse(null, "الحجز غير موجود", 404);
        }

        $user = auth()->user();

        // السماح فقط لصاحب الحجز أو الأدمن أو السكرتير المرتبط بنفس المساحة
        if (
            (int) $user->id != (int) $booking->user_id &&
            !in_array($user->role, ['admin', 'secretary']) ||
            ($user->role === 'secretary' && $user->workspace_id !== $booking->workspace_id)
        ) {
            return $this->apiResponse(null, __('messages.not_authorized'), 403);
        }
        return $this->apiResponse(new BookingResource($booking), __('messages.success'), 200);
    }


    // إنشاء حجز جديد
    public function store(Request $request, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'package_id' => 'required|exists:packages,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'number_of_hours' => 'nullable|integer|min:1|max:12',
        ]);

        $package = Package::where('id', $validated['package_id'])
            ->where('workspace_id', $validated['workspace_id'])
            ->first();

        if (!$package) {
            return $this->apiResponse(null, "الباقة المحددة لا تنتمي إلى مساحة العمل المحددة", 422);
        }
        // إذا كانت الباقة "ساعة"
        if ($package->name === 'hour') {
            if (!$request->filled('time')) {
                return $this->apiResponse(null, "يجب إدخال الوقت لباقات الساعة", 422);
            }

            $startAt = Carbon::parse($validated['date'] . ' ' . $validated['time']);
            $hours =(int) $validated['number_of_hours'] ?? 1;
            $endAt = $startAt->copy()->addHours($hours);
        } else {
            $startAt = Carbon::parse($validated['date'])->startOfDay();
            $duration = (int) $package->duration; // تحويل duration إلى int

            $endAt = match ($package->name) {
                'day' => $startAt->copy()->addDays($duration),
                'week' => $startAt->copy()->addWeeks($duration),
                'month' => $startAt->copy()->addMonths($duration),
                default => $startAt->copy()->addDays(1),
            };
        }
        $existing = Booking::where('user_id', auth()->id())
            ->where('workspace_id', $validated['workspace_id'])
            ->where('package_id', $validated['package_id'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->first();

        if ($existing) {
            return $this->apiResponse(null, "لديك طلب مشابه قيد التنفيذ بالفعل", 422);
        }
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'workspace_id' => $validated['workspace_id'],
            'package_id' => $validated['package_id'],
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => 'pending',
        ]);
        // إرسال إشعار للسكرتير بعد إنشاء الحجز
        // $userName = auth()->user()->name ?? 'مستخدم التطبيق';
        // $notificationService->notifySecretaryOfNewBooking($userName, $validated['workspace_id']);

        // Send notifications
        try {
            $sender = auth()->user(); // The user making the booking
            $workspace = Workspace::find($validated['workspace_id']);
            $secretary = $workspace->secretary; // Use the workspace's secretary relationship

            // Determine the locale from the request's Accept-Language header or fallback to 'en'
            // $locale = app()->getLocale();
            // $spaceName = $workspace->name[$locale] ?? $workspace->name['en'] ?? 'Unknown Workspace';

            if (!$secretary) {
                // Log::warning('No secretary found for workspace', [
                //     'workspace_id' => $workspace->id,
                //     'booking_id' => $booking->id,
                // ]);
            } else {
                $notificationService->sendWorkspaceReservationNotification(
                    $secretary, // Recipient (secretary)
                    $sender,    // Sender (app user)
                    $workspace,
                    true,       // Secretary notification
                    $booking->id // Reservation ID
                );
            }

            // Notify the user
            // $notificationService->sendWorkspaceReservationNotification(
            //     $sender,    // Recipient (app user)
            //     $secretary ?: $sender, // Use sender as fallback if no secretary
            //     $spaceName, // Space name
            //     false,      // User notification
            //     $booking->id // Reservation ID
            // );

            // Log::info('Workspace reservation notifications sent', [
            //     'booking_id' => $booking->id,
            //     'secretary_id' => $secretary?->id,
            //     'space_name' => $spaceName,
            //     'locale' => $locale,
            // ]);
        } catch (\Exception $e) {
            // Log::error('Failed to send workspace reservation notifications', [
            //     'booking_id' => $booking->id,
            //     'error' => $e->getMessage(),
            // ]);
        }
        return $this->apiResponse(new BookingResource($booking), __('messages.success'), 200);
    }
}
