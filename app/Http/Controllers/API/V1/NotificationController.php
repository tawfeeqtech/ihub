<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return $this->apiResponse(NotificationResource::collection($notifications), __('messages.success'), 200);
    }
}
