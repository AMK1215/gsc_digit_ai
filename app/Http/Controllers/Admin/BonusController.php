<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\BonusType;
use App\Models\User;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $agentIds = [$user->id];
        $agents = [];

        if ($user->hasRole('Master')) {
            $agentIds = User::where('agent_id', $user->id)->pluck('id')->toArray();
            $agents = $user->children()->get();
        }

        $bonuses = $this->getRequestsQuery($request, $agentIds)
            ->latest()
            ->get();

        $totalAmount = $this->getRequestsQuery($request, $agentIds)->sum('amount');

        $bonusTypes = BonusType::all();

        return view('admin.bonus.index', compact('bonuses', 'agents', 'bonusTypes', 'totalAmount'));
    }

    public function create(Request $request)
    {
        $types = BonusType::all();

        return view('admin.bonus.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => ['required'],
            'remark' => ['nullable', 'string'],
            'type_id' => ['required'],
        ]);
        $agent = Auth::user();
        $player = User::find($request->id);

        if ($agent->hasRole('Master')) {
            $agent = User::where('id', $player->agent_id)->first();
        }

        if ($agent->balanceFloat < $request->amount) {
            return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
        }

        $bonus = Bonus::create([
            'user_id' => $request->id,
            'type_id' => $request->type_id,
            'amount' => $request->amount,
            'before_amount' => $player->balanceFloat,
            'agent_id' => $player->agent_id,
            'created_id' => Auth::id(),
        ]);
        app(WalletService::class)->transfer($agent, $player, $request->amount, TransactionName::BonusLocal, ['agent_id' => Auth::id()]);
        $bonus->update([
            'after_amount' => $player->balanceFloat,
        ]);

        return redirect()->route('admin.bonus.index')->with('success', 'Bonus Added!');
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function search(Request $request)
    {
        $agent = Auth::user();
        $player = User::where('user_name', $request->user_name)->first();

        if (! $player) {
            return $this->response(false, 'Player not found');
        }

        if (! $this->isPlayerUnderAgent($player, $agent)) {
            return $this->response(false, 'This player is not your player');
        }

        $playerData = $player->only(['name', 'user_name', 'phone', 'id']);

        return $this->response(true, null, $playerData);
    }

    private function isPlayerUnderAgent(User $player, User $agent): bool
    {
        if ($agent->hasRole('Master')) {
            return $player->parent->agent_id === $agent->id;
        }

        return $player->agent_id === $agent->id;
    }

    private function response(bool $success, ?string $message = null, ?array $data = null)
    {
        $response = ['success' => $success];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    private function getRequestsQuery($request, $agentIds)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

        return Bonus::with('user')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($request->agent_id, function ($query) use ($request) {
                $query->where('agent_id', $request->agent_id);
            })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type_id', $request->type);
            })
            ->when($request->player_id, function ($query) use ($request) {
                $query->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('user_name', $request->player_id);
                });
            })
            ->whereIn('agent_id', $agentIds);
    }
}
