<?php

namespace App\Http\Controllers\Admin\Player;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerRequest;
use App\Http\Requests\TransferLogRequest;
use App\Models\Admin\UserLog;
use App\Models\PaymentType;
use App\Models\User;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PlayerController extends Controller
{
    private const PLAYER_ROLE = 4;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check for permission
        abort_if(
            Gate::denies('player_index'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden | You cannot access this page because you do not have permission'
        );

        $user = Auth::user();
        $agentIds = [$user->id];
        $agents = [];

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

        if ($user->hasRole('Master')) {
            $agentIds = User::where('agent_id', $user->id)->pluck('id')->toArray();
            $agents = $user->children()->get();
        }

        $users = User::with(['roles', 'userLog'])
            ->whereHas('roles', fn ($query) => $query->where('role_id', self::PLAYER_ROLE))
            ->when($request->player_id, fn ($query) => $query->where('user_name', $request->player_id))
            ->when($startDate && $endDate, fn ($query) => $query->whereBetween('created_at', [$startDate, $endDate]))
            ->when($request->agent_id, fn ($query) => $query->where('agent_id', $request->agent_id))
            ->when($request->phone, fn ($query) => $query->where('phone', $request->phone))
            ->when($request->last_login_ip, function ($query) use ($request) {
                $query->whereHas('userLog', fn ($logQuery) => $logQuery->where('ip_address', $request->last_login_ip));
            })
            ->whereIn('agent_id', $agentIds)
            ->orderByDesc('id')
            ->get();

            // dd($users);
        return view('admin.player.index', compact('users', 'agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(
            Gate::denies('player_create'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $player_name = $this->generateRandomString();
        //$banks = Bank::all();
        $paymentTypes = PaymentType::all();

        return view('admin.player.create', compact('player_name', 'paymentTypes'));
    }

    public function store(PlayerRequest $request)
    {
        abort_if(
            Gate::denies('player_create'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden | You cannot access this page because you do not have permission.'
        );

        try {
            $agent = $this->determineAgent($request);

            // Validate agent balance for the specified amount
            if ($this->isAmountExceedingBalance($request->amount ?? 0, $agent->balanceFloat)) {
                return redirect()->back()->with('error', 'Insufficient balance to create the player.');
            }

            // Create the player
            $player = $this->createPlayer($request, $agent);

            // Handle initial amount transfer
            if (! empty($request->amount)) {
                $this->transferInitialAmount($agent, $player, $request->amount);
            }
            UserLog::create([
                'register_ip' => $request->ip(),
                'user_id' => $player->id,
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('success', 'Player created successfully')
                ->with('url', config('app.url'))
                ->with('password', $request->password)
                ->with('user_name', $player->user_name);
        } catch (Exception $e) {
            Log::error('Error creating player: '.$e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(
            Gate::denies('player_show'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        $user_detail = User::findOrFail($id);

        return view('admin.player.show', compact('user_detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $player)
    {
        abort_if(
            Gate::denies('player_edit'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $paymentTypes = PaymentType::all();

        return response()->view('admin.player.edit', compact('player', 'paymentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $player)
    {
        $player->update($request->all());

        return redirect()->back()->with('success', 'Player updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $player)
    {
        abort_if(
            Gate::denies('user_delete'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        //$player->destroy();
        User::destroy($player->id);

        return redirect()->route('admin.player.index')->with('success', 'User deleted successfully');
    }

    public function massDestroy(Request $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }

    public function banUser($id)
    {

        $user = User::find($id);
        $user->update(['status' => $user->status == 1 ? 0 : 1]);

        return redirect()->back()->with(
            'success',
            'User '.($user->status == 1 ? 'activate' : 'inactive').' successfully'
        );
    }

    public function getCashIn(User $player)
    {
        abort_if(
            Gate::denies('make_transfer'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        return view('admin.player.cash_in', compact('player'));
    }

    public function makeCashIn(TransferLogRequest $request, User $player)
    {
        abort_if(
            Gate::denies('make_transfer'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        try {
            $inputs = $request->validated();
            $inputs['refrence_id'] = $this->getRefrenceId();

            $agent = Auth::user();

            if ($agent->hasRole('Master')) {
                $agent = User::where('id', $player->agent_id)->first();
            }

            $cashIn = $inputs['amount'];

            if ($cashIn > $agent->balanceFloat) {

                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            app(WalletService::class)->transfer(
                $agent,
                $player,
                $request->validated('amount'),
                TransactionName::CreditTransfer,
                ['note' => $request->note ?? '', 'agent_id' => Auth::id()],
            );

            return redirect()->back()
                ->with('success', 'CashIn submitted successfully!');
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getCashOut(User $player)
    {
        abort_if(
            Gate::denies('make_transfer'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        return view('admin.player.cash_out', compact('player'));
    }

    public function makeCashOut(TransferLogRequest $request, User $player)
    {
        abort_if(
            Gate::denies('make_transfer'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );

        try {
            $inputs = $request->validated();
            $inputs['refrence_id'] = $this->getRefrenceId();

            $agent = Auth::user();
            $cashOut = $inputs['amount'];

            if ($agent->hasRole('Master')) {
                $agent = User::where('id', $player->agent_id)->first();
            }
            if ($cashOut > $player->balanceFloat) {

                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            app(WalletService::class)->transfer(
                $player,
                $agent,
                $request->validated('amount'),
                TransactionName::DebitTransfer,
                ['note' => $request->note ?? '', 'agent_id' => Auth::id()]
            );

            return redirect()->back()
                ->with('success', 'CashOut submitted successfully!');
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getChangePassword($id)
    {
        $player = User::find($id);

        return view('admin.player.change_password', compact('player'));
    }

    public function makeChangePassword($id, Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $player = User::find($id);
        $player->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()
            ->with('success', 'Player Change Password successfully')
            ->with('password', $request->password)
            ->with('user_name', $player->user_name);
    }

    private function generateRandomString()
    {
        $latestPlayer = User::where('type', UserType::Player)->latest('id')->first();

        $nextNumber = $latestPlayer ? intval(substr($latestPlayer->user_name, 3)) + 1 : 1;

        return 'MKM'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function getRefrenceId($prefix = 'REF')
    {
        return uniqid($prefix);
    }

    private function isExistAgent($referralCode)
    {
        return User::where('referral_code', $referralCode)->where('agent_id', Auth::id())->first();
    }

    private function determineAgent($request)
    {
        $agent = Auth::user();

        if ($agent->hasRole('Master')) {
            if (! empty($request->referral_code)) {
                $agent = $this->isExistAgent($request->referral_code);
                if (! $agent) {
                    throw new Exception('The referral code is not your agent code.');
                }
            } else {
                $agent = User::findOrFail(4); // Default agent (fallback)
            }
        }

        return $agent;
    }

    private function isAmountExceedingBalance($amount, $balance)
    {
        return $amount > $balance;
    }

    private function createPlayer($request, $agent)
    {
        $player = User::create([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'agent_id' => $agent->id ?? Auth::id(),
            'type' => UserType::Player,
        ]);

        $player->roles()->sync(self::PLAYER_ROLE);

        return $player;
    }

    private function transferInitialAmount($agent, $player, $amount)
    {
        app(WalletService::class)->transfer($agent, $player, $amount, TransactionName::CreditTransfer, ['agent_id' => Auth::id()]);
    }
}
