<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Http\Resources\PackageResource;
use App\Http\Resources\WorkspacePackagesResource;
use App\Models\Workspace;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index($workspaceId)
    {
        $workspace = Workspace::with('packages')->findOrFail($workspaceId);
        return $this->apiResponse(new WorkspacePackagesResource($workspace), __('messages.success'), 200);
    }
}
