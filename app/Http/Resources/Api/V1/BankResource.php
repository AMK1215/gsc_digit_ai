<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bank_id' => $this->paymentType->id,
            'bank' => $this->paymentType->name,
            'logo' => $this->paymentType->img_url,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
        ];
    }
}
