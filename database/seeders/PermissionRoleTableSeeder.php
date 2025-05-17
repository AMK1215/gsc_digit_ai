<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner_permissions = Permission::whereIn('title', [
            'owner_access',
            'master_index',
            'master_create',
            'master_edit',
            'master_delete',
            'transfer_log',
            'make_transfer',
            'game_type_access',
            'contact',
            'agent_change_password_access',

        ]);
        Role::findOrFail(1)->permissions()->sync($owner_permissions->pluck('id'));
        // master permissions
        $master_permissions = Permission::whereIn('title', [
            'master_access',
            'agent_access',
            'transfer_log',
            'agent_index',
            'agent_create',
            'agent_edit',
            'agent_delete',
            'player_index',
            'player_create',
            'player_edit',
            'player_delete',
            'make_transfer',
            'withdraw_requests',
            'deposit_requests',
            'agent_change_password_access',

        ]);
        Role::findOrFail(2)->permissions()->sync($master_permissions->pluck('id'));

        // Agent gets specific permissions
        $agent_permissions = Permission::whereIn('title', [
            'agent_access',
            'player_index',
            'player_create',
            'player_edit',
            'player_delete',
            'transfer_log',
            'make_transfer',
            'payment_type',
            'withdraw_requests',
            'deposit_requests',
            'contact',
            'agent_change_password_access',
        ])->pluck('id');
        Role::findOrFail(3)->permissions()->sync($agent_permissions);

        $systemWallet = Permission::where('title', 'system_wallet')->first();
        Role::findOrFail(5)->permissions()->sync($systemWallet);

        $subAgent = Permission::whereIn('title', [
            'withdraw_requests',
            'deposit_requests',
        ])->pluck('id');
        Role::findOrFail(6)->permissions()->sync($subAgent);
    }
}
