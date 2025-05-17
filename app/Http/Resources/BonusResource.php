<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BonusResource extends JsonResource
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
            'user_name' => $this->user->user_name,
            'type_id' => $this->type->name,
            'amount' => $this->amount,
            'before_amount' => $this->before_amount,
            'after_amount' => $this->after_amount,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
