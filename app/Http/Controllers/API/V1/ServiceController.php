<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\App;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function index($workspaceId)
    {
        $lang = App::getLocale(); // اللغة المفعّلة من الهيدر

        $workspace = Workspace::with('services')->findOrFail($workspaceId);

        $services = collect([
            [
                'id' => 0,
                'name' => __('messages.change_seat'),
                'type' => 'seat_change'
            ]
        ]);

        foreach ($workspace->services as $service) {
            $services->push([
                'id' => $service->id,
                'name' => $service->name[$lang] ?? $service->name['en'] ?? '',
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
