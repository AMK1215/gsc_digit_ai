<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PromotionResource;
use App\Models\Admin\Promotion;
use App\Traits\HttpResponses;

class PromotionController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $data = Promotion::agentPlayer()->latest()->get();

        return $this->success(PromotionResource::collection($data));

    }

    public function show($id)
    {
        $promotion = Promotion::find($id);

        return $this->success($promotion);
    }
}
