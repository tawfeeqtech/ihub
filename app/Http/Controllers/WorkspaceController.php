<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        // Fetch latest workspaces (e.g., last 3 added)
        $latestWorkspaces = Workspace::with('images')
            ->latest()
            ->take(3)
            ->get();

        // Fetch governorates for the search filter
        $governorates = Governorate::all();

        return view('home', compact('latestWorkspaces', 'governorates'));
    }

    public function workspaces(Request $request)
    {
        $query = Workspace::with(['governorate', 'region', 'images']);

        // Apply search filters
        if ($request->filled('name')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ['%' . $request->input('name') . '%'])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ['%' . $request->input('name') . '%']);
        }

        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->input('governorate_id'));
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->input('region_id'));
        }

        if ($request->has('has_free')) {
            $query->where('has_free', $request->input('has_free') == '1');
        }

        if ($request->has('has_evening_shift')) {
            $query->where('has_evening_shift', $request->input('has_evening_shift') == '1');
        }

        if ($request->has('bank_payment_supported')) {
            $query->where('bank_payment_supported', $request->input('bank_payment_supported') == '1');
        }

        $workspaces = $query->get();
        $governorates = Governorate::all();

        return view('workspaces', compact('workspaces', 'governorates'));
    }

    public function show(Workspace $workspace)
    {
        $workspace->load(['governorate', 'region', 'images']);
        return view('workspace-details', compact('workspace'));
    }
}
?>
