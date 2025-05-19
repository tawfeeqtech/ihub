<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspacePackagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'payment' => [
                'bank_payment_supported' => $this->bank_payment_supported,
                'bank_account_number' => $this->bank_account_number,
                'mobile_payment_number' => $this->mobile_payment_number,
            ],
            'packages' => PackageResource::collection($this->packages),
        ];
    }
}
