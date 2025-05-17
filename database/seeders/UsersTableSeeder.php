<?php

namespace Database\Seeders;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Models\User;
use App\Services\WalletService;
use App\Settings\AppSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = $this->createUser(UserType::Admin, 'Owner', 'monkeyking', '09123456789');
        (new WalletService)->deposit($admin, 10 * 100_00000, TransactionName::CapitalDeposit);

        $master = $this->createUser(UserType::Master, 'Master 1', 'MK898437', '09112345678', $admin->id);
        (new WalletService)->transfer($admin, $master, 8 * 100_0000, TransactionName::CreditTransfer);

        $master_2 = $this->createUser(UserType::Master, 'Master 2', 'MK898438', '09112345679', $admin->id);
        (new WalletService)->transfer($admin, $master_2, 1 * 100_0000, TransactionName::CreditTransfer);

        $agent_1 = $this->createUser(UserType::Agent, 'Agent 1', 'MKA898737', '09112345674', $master->id, 'vH4HueE9');
        (new WalletService)->transfer($master, $agent_1, 1 * 100_0000, TransactionName::CreditTransfer);

        $agent_2 = $this->createUser(UserType::Agent, 'Agent 2', 'MKA898738', '09112345675', $master->id, '4Hvqiu7G');
        (new WalletService)->transfer($master, $agent_2, 2 * 100_0000, TransactionName::CreditTransfer);

        $agent_3 = $this->createUser(UserType::Agent, 'Agent 3', 'MKA898739', '09112345676', $master->id, 'i0Yvb4df');
        (new WalletService)->transfer($master, $agent_3, 2 * 100_0000, TransactionName::CreditTransfer);

        $agent_4 = $this->createUser(UserType::Agent, 'Agent 4', 'MKA898740', '09112345677', $master_2->id, 'r9Bv51Qh');
        (new WalletService)->transfer($master_2, $agent_4, 2 * 100_000, TransactionName::CreditTransfer);

        $player_1 = $this->createUser(UserType::Player, 'Player 1', 'SPM000001', '09111111111', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player_1, 30000, TransactionName::CreditTransfer);

        $player2 = $this->createUser(UserType::Player, 'Player3', 'SPM000003', '09111111113', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player2, 0.00, TransactionName::CreditTransfer);
        $player3 = $this->createUser(UserType::Player, 'Player4', 'SPM000004', '09111111114', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player3, 0.00, TransactionName::CreditTransfer);
        $player4 = $this->createUser(UserType::Player, 'Player5', 'SPM000005', '09111111115', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player4, 0.00, TransactionName::CreditTransfer);

        $systemWallet = $this->createUser(UserType::SystemWallet, 'SystemWallet', 'systemWallet', '09222222222');
        (new WalletService)->deposit($systemWallet, 50 * 100_0000, TransactionName::CapitalDeposit);

    }

    private function createUser(UserType $type, $name, $user_name, $phone, $parent_id = null, $referral_code = null)
    {
        return User::create([
            'name' => $name,
            'user_name' => $user_name,
            'phone' => $phone,
            'password' => Hash::make('delightmyanmar'),
            'agent_id' => $parent_id,
            'status' => 1,
            'type' => $type->value,
            'referral_code' => $referral_code,
        ]);
    }
}
