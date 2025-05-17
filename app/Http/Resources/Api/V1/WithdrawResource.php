<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
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
            'agent_id' => $this->agent_id,
            'user_id' => $this->user_id,
            'account_name' => $this->account_name,
            'account_number' => $this->account_no,
            'amount' => $this->amount,
            'payment_type' => $this->paymentType->name,
            'status' => $this->status == 1 ? 'approved' : ($this->status == 2 ? 'rejected' : 'pending'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
