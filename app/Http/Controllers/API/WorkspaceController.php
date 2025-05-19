<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Http\Resources\WorkspaceResource;
use App\Http\Resources\WorkspaceShowResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    use ApiResponseTrait;

    // عرض قائمة المساحات مع إمكانية الفلترة
    public function index(Request $request)
    {
        $query = Workspace::query();
        // فلترة حسب الموقع
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        // فلترة حسب آلية الدفع
        if ($request->filled('bank_payment_supported') && $request->bank_payment_supported == true) {
            $query->where('bank_payment_supported', true);
        }
        // بحث بالنص
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $workspaces = $query->latest()->get();
        return $this->apiResponse(WorkspaceResource::collection($workspaces), "Success", 200);
    }
    public function show($id)
    {
        $workspace = Workspace::with(['secretary', 'images'])->findOrFail($id);
        return $this->apiResponse(new WorkspaceShowResource($workspace), "Success", 200);
    }
}
