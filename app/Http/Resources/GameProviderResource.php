<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameProviderResource extends JsonResource
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
            'name' => $this->provider_name,
            'code' => $this->provider_code,
            'order' => $this->order,
            'status' => $this->status,
            'image' => $this->imgUrl,
            'game_type' => $this->game_type,
        ];
    }
}
