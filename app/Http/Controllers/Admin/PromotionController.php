<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Promotion;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PromotionController extends Controller
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
        $promotions = $auth->hasPermission('master_access') ? Promotion::query()->master()->latest()->get() : Promotion::query()->agent()->latest()->get();

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.promotions.create');
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
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);

        $type = $request->type ?? 'single';
        $filename = $this->handleImageUpload($request->image, 'promotions');

        if ($type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);

            Promotion::create([
                'image' => $filename,
                'agent_id' => $agentId,
                'description' => $request->description,

            ]);
        } elseif ($type === 'all') {
            foreach ($user->agents as $agent) {
                Promotion::create([
                    'image' => $filename,
                    'agent_id' => $agent->id,
                    'description' => $request->description,
                ]);
            }
        }

        return redirect(route('admin.promotions.index'))->with('success', 'New Promotions Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }
        $this->FeaturePermission($promotion->agent_id);

        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }
        $this->FeaturePermission($promotion->agent_id);

        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }
        $this->FeaturePermission($promotion->agent_id);
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
        ]);
        $this->handleImageDelete($promotion->image, 'promotions');
        $filename = $this->handleImageUpload($request->image, 'promotions');
        $promotion->update([
            'image' => $filename,
            'description' => $request->description,
        ]);

        return redirect(route('admin.promotions.index'))->with('success', 'Promotion Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }
        $this->FeaturePermission($promotion->agent_id);
        $this->handleImageDelete($promotion->image, 'promotions');
        $promotion->delete();

        return redirect()->back()->with('success', 'Promotion Deleted.');
    }
}
