<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AdsBannerResource;
use App\Http\Resources\Api\V1\BannerResource;
use App\Http\Resources\Api\V1\BannerTextResource;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Traits\HttpResponses;

class BannerController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $data = Banner::agentPlayer()->get();

        // return $this->success($data);
        return $this->success(BannerResource::collection($data));
    }

    public function bannerText()
    {
        $data = BannerText::agentPlayer()->latest()->first();

        return $this->success(new BannerTextResource($data));
    }

    public function AdsBannerIndex()
    {
        $data = BannerAds::agentPlayer()->latest()->first();

        return $this->success(new AdsBannerResource($data));
    }
}
