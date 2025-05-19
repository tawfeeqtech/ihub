<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Models\WorkspaceImage;
use Illuminate\Support\Facades\File;

class WorkspaceImageController extends Controller
{
    public function create(Workspace $workspace)
    {
        return view('admin.upload-images', compact('workspace'));
    }

    public function store(Request $request, Workspace $workspace)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        try {

            foreach ($request->file('images') as $image) {
                if ($image) {
                    // تخزين الصورة
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $destinationPath = public_path('uploads');

                    // أنشئ المجلد إذا مش موجود
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $image->move($destinationPath, $filename);

                    // إنشاء سجل جديد في قاعدة البيانات
                    WorkspaceImage::create([
                        'workspace_id' => $workspace->id,
                        'image' => 'uploads/' . $filename,
                    ]);
                }
            }

            // إعادة توجيه المستخدم مع رسالة نجاح
            return redirect()->back()->with('success', 'تم رفع الصور بنجاح');
        } catch (\Exception $e) {
            // في حالة حدوث خطأ
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء رفع الصور. حاول مرة أخرى.']);
        }

        return redirect()->back()->with('success', 'تم رفع الصور بنجاح');
    }

    public function destroy(WorkspaceImage $image)
    {
        // التحقق من الصلاحية: هل المستخدم مرتبط بنفس المساحة أو مدير؟
        $user = auth()->user();

        if (
            $user->role !== 'admin' &&
            (! $user->workspace_id || $user->workspace_id !== $image->workspace_id)
        ) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        // حذف الصورة من السيرفر
        $imagePath = public_path($image->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // حذف السجل من قاعدة البيانات
        $image->delete();

        return redirect()->back()->with('success', 'تم حذف الصور بنجاح');
    }
}
