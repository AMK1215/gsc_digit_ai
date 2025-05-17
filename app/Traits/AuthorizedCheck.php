<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizedCheck
{
    protected function FeaturePermission($agentId)
    {
        $user = Auth::user();
        $master = $user->hasRole('Master');

        $isAuthorized = $master ? in_array($agentId, $user->agents()->pluck('id')->toArray()) : $user->id === $agentId;
        if ($isAuthorized) {
            return true;
        } else {
            abort(403, 'Unauthorized');
        }
    }

    protected function MasterAgentRoleCheck()
    {
        $user = Auth::user();
        $master_access = $user->hasPermission('master_access');
        $agent_access = $user->hasPermission('agent_access');
        if ($master_access || $agent_access) {
            return true;
        } else {
            abort(403, 'Unauthorized');
        }
    }
}
