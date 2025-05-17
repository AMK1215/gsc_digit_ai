<?php

namespace App\Http\Controllers\Admin\BannerAds;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerAds;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BannerAdsController extends Controller
{
    use AuthorizedCheck;

    /**
     * Display a listing of the resource.
     */
    use ImageUpload;

    public function index()
    {
        $auth = Auth::user();
        $this->MasterAgentRoleCheck();
        $banners = $auth->hasPermission('master_access') ? BannerAds::query()->master()->latest()->get() : BannerAds::query()->agent()->latest()->get();

        return view('admin.banner_ads.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.banner_ads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');

        // Validate the request
        $request->validate([
            'mobile_image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'desktop_image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
            'description' => 'nullable',
        ]);
        $type = $request->type ?? 'single';
        $mobile_image = $this->handleImageUpload($request->mobile_image, 'banners_ads');
        $desktop_image = $this->handleImageUpload($request->desktop_image, 'banners_ads');

        if ($type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);
            BannerAds::create([
                'mobile_image' => $mobile_image,
                'desktop_image' => $desktop_image,
                'agent_id' => $agentId,
                'description' => $request->description,
            ]);
        } elseif ($type === 'all') {
            foreach ($user->agents as $agent) {
                BannerAds::create([
                    'mobile_image' => $mobile_image,
                    'desktop_image' => $desktop_image,
                    'agent_id' => $agent->id,
                    'description' => $request->description,
                ]);
            }
        }

        return redirect(route('admin.adsbanners.index'))->with('success', 'New Ads Banner Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);

        return view('admin.banner_ads.show', compact('adsbanner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);

        return view('admin.banner_ads.edit', compact('adsbanner'));
    }

    public function update(Request $request, BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();

        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }

        $this->FeaturePermission($adsbanner->agent_id);

        $request->validate([
            'mobile_image' => 'image|max:2048',
            'desktop_image' => 'image|max:2048',
        ]);

        $this->deleteImagesIfProvided($adsbanner, $request);

        $updateData = $this->prepareUpdateData($request, $adsbanner);

        $adsbanner->update($updateData);

        return redirect(route('admin.adsbanners.index'))->with('success', 'Ads Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);
        $this->handleImageDelete($adsbanner->mobile_image, 'banners_ads');
        $this->handleImageDelete($adsbanner->desktop_image, 'banners_ads');

        $adsbanner->delete();

        return redirect()->back()->with('success', 'Ads Banner Deleted.');
    }

    /**
     * Delete images if new ones are provided in the request.
     */
    private function deleteImagesIfProvided(BannerAds $adsbanner, Request $request): void
    {
        if ($request->hasFile('mobile_image')) {
            $this->handleImageDelete($adsbanner->mobile_image, 'banners');
        }

        if ($request->hasFile('desktop_image')) {
            $this->handleImageDelete($adsbanner->desktop_image, 'banners');
        }
    }

    /**
     * Prepare data for updating the banner.
     */
    private function prepareUpdateData(Request $request, BannerAds $adsbanner): array
    {
        $updateData = ['description' => $request->input('description')];

        if ($request->hasFile('mobile_image')) {
            $updateData['mobile_image'] = $this->handleImageUpload($request->file('mobile_image'), 'banners_ads');
        } else {
            $updateData['mobile_image'] = $adsbanner->mobile_image;
        }

        if ($request->hasFile('desktop_image')) {
            $updateData['desktop_image'] = $this->handleImageUpload($request->file('desktop_image'), 'banners_ads');
        } else {
            $updateData['desktop_image'] = $adsbanner->desktop_image;
        }

        return $updateData;
    }
}
