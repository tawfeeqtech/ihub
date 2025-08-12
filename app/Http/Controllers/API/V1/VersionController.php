<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;

class VersionController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $data = [
            'version' => 'v1',
            'latest' => true,
            'supported' => ['v1'],
            'deprecated' => [],
        ];

        // Return the version information as a JSON response
        return $this->apiResponse($data, __('messages.success'), 200);
    }
}
