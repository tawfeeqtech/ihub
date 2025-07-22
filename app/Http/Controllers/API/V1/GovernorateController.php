<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\GovernorateResource;
use App\Models\Governorate;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;

class GovernorateController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $governorates = Governorate::with('regions')->get();
        return $this->apiResponse(GovernorateResource::collection($governorates), __('messages.success'), 200);
    }
}