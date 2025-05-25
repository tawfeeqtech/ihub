<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Traits\ApiResponseTrait;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function index($workspaceId)
    {
        $workspace = Workspace::with('services')->findOrFail($workspaceId);

        // نبدأ بإضافة خدمة تغيير المقعد يدويًا
        $services = collect([
            ['name' => 'تغيير المقعد', 'type' => 'seat_change']
        ]);

        // نضيف باقي الخدمات المخزنة من السكرتيرة
        foreach ($workspace->services as $service) {
            $services->push([
                'id' => $service->id,
                'name' => $service->name,
                'type' => 'cafe_request',
            ]);
        }
        $data = [
            'workspace_id' => $workspace->id,
            'services' => $services,
        ];
        return $this->apiResponse($data, __('messages.success'), 200);
    }
}
