<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotGameDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imgUrl = $this->gameTypes[0]->pivot->image;

        return [
            'id' => $this->id,
            'provider_name' => $this->provider_name,
            'imgUrl' => asset('assets/img/provider_logo/'.$imgUrl),
            'hot_lists' => $this->gameLists->map(function ($game) {
                return [
                    'code' => $game->game_code,
                    'name' => $game->game_name,
                    'game_type_id' => $game->game_type_id,
                    'provider_id' => $game->product_id,
                    'image' => $game->image_url,
                ];
            }),
        ];
    }
}
