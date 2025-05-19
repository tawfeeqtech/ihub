<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Http\Resources\BookingResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        return $this->apiResponse(BookingResource::collection($bookings), "Success", 200);
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
            $user->id !== $booking->user_id &&
            !in_array($user->role, ['admin', 'secretary']) ||
            ($user->role === 'secretary' && $user->workspace_id !== $booking->workspace_id)
        ) {
            return $this->apiResponse(null, "غير مصرح", 403);
        }
        return $this->apiResponse(new BookingResource($booking), "Success", 200);
    }


    // إنشاء حجز جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'package_id' => 'required|exists:packages,id',
            'date' => 'required|date', // تاريخ الحجز دائمًا مطلوب
            'time' => 'nullable|date_format:H:i', // الوقت فقط في حالة باقة الساعات
            'number_of_hours' => 'nullable|integer|min:1|max:12', // عدد الساعات إذا كانت باقة الساعة
        ]);

        $package = Package::where('id', $validated['package_id'])
            ->where('workspace_id', $validated['workspace_id'])
            ->first();

        if (!$package) {
            return $this->apiResponse(null, "الباقة المحددة لا تنتمي إلى مساحة العمل المحددة", 422);
        }
        // إذا كانت الباقة "ساعة"
        if ($package->name === 'ساعة') {
            if (!$request->filled('time')) {
                return $this->apiResponse(null, "يجب إدخال الوقت لباقات الساعة", 422);
            }

            $startAt = Carbon::parse($validated['date'] . ' ' . $validated['time']);
            $hours = $validated['number_of_hours'] ?? 1;
            $endAt = $startAt->copy()->addHours($hours);
        } else {
            // باقي الباقات: نأخذ فقط التاريخ كبداية من منتصف الليل
            $startAt = Carbon::parse($validated['date'])->startOfDay();

            $endAt = match ($package->name) {
                'يوم' => $startAt->copy()->addDays($package->duration),
                'أسبوع' => $startAt->copy()->addWeeks($package->duration),
                'شهر' => $startAt->copy()->addMonths($package->duration),
                default => $startAt->copy()->addDays(1), // fallback ليوم افتراضي
            };
        }

        // التحقق من التكرار
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

        return $this->apiResponse(new BookingResource($booking), "Success", 200);
    }
}
