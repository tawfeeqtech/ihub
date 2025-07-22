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

    public function index(Request $request)
    {
        $request->validate([
            'governorate_id' => 'nullable|exists:governorates,id',
            'region_id' => 'nullable|exists:regions,id',
            'has_free' => 'nullable|boolean',
            'has_evening_shift' => 'nullable|boolean',
            'bank_payment_supported' => 'nullable|boolean',
            'location' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        $query = Workspace::query()->with('images');

        // Filter by governorate
        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        // Filter by region
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Filter by free status
        if ($request->filled('has_free')) {
            $query->where('has_free', filter_var($request->has_free, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by evening shift
        if ($request->filled('has_evening_shift')) {
            $query->where('has_evening_shift', filter_var($request->has_evening_shift, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by bank payment support
        if ($request->filled('bank_payment_supported')) {
            $query->where('bank_payment_supported', filter_var($request->bank_payment_supported, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by location (JSON search)
        if ($request->filled('location')) {
            $query->searchJsonField('location', $request->location);
        }

        // Search by name (JSON search)
        if ($request->filled('search')) {
            $query->searchJsonField('name', $request->search);
        }

        $workspaces = $query->latest()->get();
        return $this->apiResponse(WorkspaceResource::collection($workspaces), __('messages.success'), 200);
    }
    public function show($id)
    {
        $workspace = Workspace::with(['secretary', 'images'])->findOrFail($id);
        return $this->apiResponse(new WorkspaceShowResource($workspace), __('messages.success'), 200);
    }
}
