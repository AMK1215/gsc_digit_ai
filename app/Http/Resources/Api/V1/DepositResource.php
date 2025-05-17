<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'account_name' => $this->bank->account_name,
            'account_number' => $this->bank->account_number,
            'payment_type' => $this->bank->paymentType->name,
            'status' => $this->status == 1 ? 'approved' : ($this->status == 2 ? 'rejected' : 'pending'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
