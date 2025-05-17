<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerText;
use App\Traits\AuthorizedCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerTextController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizedCheck;

    public function index()
    {
        $auth = auth()->user();
        $this->MasterAgentRoleCheck();
        $texts = $auth->hasPermission('master_access') ? BannerText::query()->master()->latest()->get() : BannerText::query()->agent()->latest()->get();

        return view('admin.banner_text.index', compact('texts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.banner_text.create');
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
            'text' => 'required',
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);
        $type = $request->type ?? 'single';
        if ($type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);

            BannerText::create([
                'text' => $request->text,
                'agent_id' => $agentId,
            ]);
        } elseif ($type === 'all') {
            foreach ($user->agents as $agent) {
                BannerText::create([
                    'text' => $request->text,
                    'agent_id' => $agent->id,
                ]);
            }
        }

        return redirect(route('admin.text.index'))->with('success', 'New Banner Text Created Successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }

        return view('admin.banner_text.show', compact('text'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }

        return view('admin.banner_text.edit', compact('text'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }
        $this->FeaturePermission($text->agent_id);
        $data = $request->validate([
            'text' => 'required',
        ]);
        $text->update($data);

        return redirect(route('admin.text.index'))->with('success', 'Banner Text Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }
        $this->FeaturePermission($text->agent_id);
        $text->delete();

        return redirect()->back()->with('success', 'Banner Text Deleted Successfully.');
    }
}
