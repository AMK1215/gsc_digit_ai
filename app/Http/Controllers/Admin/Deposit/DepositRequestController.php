<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\PaymentType;
use App\Models\User;
use App\Models\UserPayment;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $agentIds = [$user->id];
        $agents = [];

        if ($user->hasRole('Master')) {
            $agentIds = $this->getAgentIds($request, $user);
            $agents = $user->children()->get();
        }

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

        $deposits = $this->getDepositRequestsQuery($request, $agentIds, $startDate, $endDate)
            ->latest()
            ->get();

        $paymentTypes = PaymentType::all();

        $totalAmount = $this->getDepositRequestsQuery($request, $agentIds, $startDate, $endDate)
            ->sum('amount');

        return view('admin.deposit_request.index', compact('deposits', 'agents', 'paymentTypes', 'totalAmount'));
    }

    public function statusChangeIndex(Request $request, DepositRequest $deposit)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
            'amount' => 'required|numeric|min:0',
            'player' => 'required|exists:users,id',
        ]);

        try {
            $agent = Auth::user();
            $player = User::find($request->player);

            if ($agent->hasRole('Master')) {
                $agent = User::where('id', $player->agent_id)->first();
            }

            if ($request->status == 1 && $agent->balanceFloat < $request->amount) {
                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            $deposit->update([
                'status' => $request->status,
                'before_amount' => $player->balanceFloat,
            ]);

            if ($request->status == 1) {
                app(WalletService::class)->transfer($agent, $player, $request->amount, TransactionName::CreditTransfer, ['agent_id' => Auth::id()]);
            }

            $deposit->update([
                'status' => $request->status,
                'after_amount' => $player->balanceFloat,
            ]);

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusChangeReject(Request $request, DepositRequest $deposit)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        try {
            // Update the deposit status
            $deposit->update([
                'status' => $request->status,
            ]);

            return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(DepositRequest $deposit)
    {
        return view('admin.deposit_request.show', compact('deposit'));
    }

    private function getAgentIds($request, $user)
    {
        if ($request->agent_id) {
            return User::where('id', $request->agent_id)->pluck('id')->toArray();
        }

        return User::where('agent_id', $user->id)->pluck('id')->toArray();
    }

    private function getDepositRequestsQuery($request, $agentIds, $startDate, $endDate)
    {
        return DepositRequest::with('bank')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($request->player_id, function ($query) use ($request) {
                $query->whereHas('user', function ($subQuery) use ($request) {
                    $subQuery->where('user_name', $request->player_id);
                });
            })
            ->when($request->agent_id, function ($query) use ($request) {
                $query->where('agent_id', $request->agent_id);
            })
            ->when($request->payment_type_id, function ($query) use ($request) {
                $query->whereHas('bank', function ($subQuery) use ($request) {
                    $subQuery->where('payment_type_id', $request->payment_type_id);
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->whereIn('agent_id', $agentIds);
    }
}
