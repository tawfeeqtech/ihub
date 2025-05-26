<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Http\Resources\WorkspaceResource;
use App\Http\Resources\WorkspaceShowResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class WorkspaceController extends Controller
{
    use ApiResponseTrait;

    // عرض قائمة المساحات مع إمكانية الفلترة
    public function index(Request $request)
    {
        $query = Workspace::query()->with('images');
        // فلترة حسب الموقع
        if ($request->filled('location')) {
            $query->searchJsonField('location', $request->location);
        }
        // فلترة حسب آلية الدفع
        if ($request->filled('bank_payment_supported') && $request->bank_payment_supported == true) {
            $query->where('bank_payment_supported', true);
        }
        // بحث بالنص
        if ($request->filled('search')) {
            $query->searchJsonField('name', $request->search);
        }
        $workspaces = $query->latest()->get();
        return $this->apiResponse(WorkspaceResource::collection($workspaces),  __('messages.success'), 200);
    }
    public function show($id)
    {
        $workspace = Workspace::with(['secretary', 'images'])->findOrFail($id);
        return $this->apiResponse(new WorkspaceShowResource($workspace), __('messages.success'), 200);
    }
}
