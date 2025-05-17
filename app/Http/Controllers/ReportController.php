<?php

namespace App\Http\Controllers;

use App\Models\Admin\Product;
use App\Models\Webhook\BetNResult;
use App\Models\Webhook\Result;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $adminId = auth()->id();

        $results = $this->buildQuery($request, $adminId)->get();

        return view('report.index', compact('results'));
    }

    public function detail(Request $request, $playerId)
    {
        $details = $this->getPlayerDetails($playerId, $request);

        $productTypes = Product::where('is_active', 1)->get();

        return view('report.detail', compact('details', 'productTypes', 'playerId'));
    }

    private function buildQuery(Request $request, $adminId)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

        $resultsSubquery = Result::select(
            'results.user_id',
            DB::raw('SUM(results.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(results.win_amount) as win_amount'),
            DB::raw('SUM(results.net_win) as net_win')
        )
            ->groupBy('results.user_id')
            ->whereBetween('results.created_at', [$startDate, $endDate]);

        $betsSubquery = BetNResult::select(
            'bet_n_results.user_id',
            DB::raw('SUM(bet_n_results.bet_amount) as bet_total_bet_amount'),
            DB::raw('SUM(bet_n_results.win_amount) as bet_total_win_amount'),
            DB::raw('SUM(bet_n_results.net_win) as bet_total_net_amount')
        )
            ->groupBy('bet_n_results.user_id')
            ->whereBetween('bet_n_results.created_at', [$startDate, $endDate]);

        $query = DB::table('users as players')
            ->select(
                'players.id as user_id',
                'players.name as player_name',
                'players.user_name as user_name',
                'agents.name as agent_name',
                DB::raw('IFNULL(results.total_bet_amount, 0) + IFNULL(bets.bet_total_bet_amount, 0) as total_bet_amount'),
                DB::raw('IFNULL(results.win_amount, 0) + IFNULL(bets.bet_total_win_amount, 0) as total_win_amount'),
                DB::raw('IFNULL(results.net_win, 0) + IFNULL(bets.bet_total_net_amount, 0) as total_net_win'),
                DB::raw('MAX(wallets.balance) as balance'),
                DB::raw('IFNULL(deposit_requests.total_amount, 0) as deposit_amount'),
                DB::raw('IFNULL(with_draw_requests.total_amount, 0) as withdraw_amount'),
                DB::raw('IFNULL(bonuses.total_amount, 0) as bonus_amount')
            )
            ->leftJoin('users as agents', 'players.agent_id', '=', 'agents.id')
            ->leftJoin('wallets', 'wallets.holder_id', '=', 'players.id')
            ->leftJoinSub($resultsSubquery, 'results', 'results.user_id', '=', 'players.id') // Fixed alias
            ->leftJoinSub($betsSubquery, 'bets', 'bets.user_id', '=', 'players.id') // Fixed alias
            ->leftJoin($this->getSubquery('bonuses', $startDate, $endDate, 'status = 1'), 'bonuses.user_id', '=', 'players.id')
            ->leftJoin($this->getSubquery('deposit_requests', $startDate, $endDate, 'status = 1'), 'deposit_requests.user_id', '=', 'players.id')
            ->leftJoin($this->getSubquery('with_draw_requests', $startDate, $endDate, 'status = 1'), 'with_draw_requests.user_id', '=', 'players.id')
            ->when($request->player_id, fn ($query) => $query->where('players.user_name', $request->player_id))
            ->where(function ($query) {
                $query->whereNotNull('results.user_id')
                    ->orWhereNotNull('bets.user_id');
            });

        $this->applyRoleFilter($query, $adminId);

        return $query->groupBy('players.id', 'players.name', 'players.user_name', 'agents.name');
    }

    private function applyRoleFilter($query, $adminId)
    {
        if (Auth::user()->hasRole('Master')) {
            $query->where('agents.agent_id', $adminId);
        } elseif (Auth::user()->hasRole('Agent')) {
            $query->where('agents.id', $adminId);
        }
    }

    private function getPlayerDetails($playerId, $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

        $combinedSubquery = DB::table('results')
            ->select(
                'user_id',
                'total_bet_amount',
                'win_amount',
                'net_win',
                'game_lists.game_name',
                'products.provider_name',
                'results.created_at as date'
            )
            ->join('game_lists', 'game_lists.game_id', '=', 'results.game_code')
            ->join('products', 'products.id', '=', 'game_lists.product_id')
            ->whereBetween('results.created_at', [$startDate, $endDate])
            ->when($request->product_id, fn ($query) => $query->where('products.id', $request->product_id))
            ->unionAll(
                DB::table('bet_n_results')
                    ->select(
                        'user_id',
                        'bet_amount as total_bet_amount',
                        'win_amount',
                        'net_win',
                        'game_lists.game_name',
                        'products.provider_name',
                        'bet_n_results.created_at as date'
                    )
                    ->join('game_lists', 'game_lists.game_id', '=', 'bet_n_results.game_code')
                    ->join('products', 'products.id', '=', 'game_lists.product_id')
                    ->whereBetween('bet_n_results.created_at', [$startDate, $endDate])
                    ->when($request->product_id, fn ($query) => $query->where('products.id', $request->product_id))
            );

        $query = DB::table('users as players')
            ->joinSub($combinedSubquery, 'combined', 'combined.user_id', '=', 'players.id')
            ->where('players.id', $playerId);

        return $query->orderBy('date', 'desc')->get();
    }

    private function getSubquery($table, $startDate, $endDate, $condition)
    {
        return DB::raw("(SELECT user_id, SUM(amount) AS total_amount FROM $table WHERE $condition  and created_at between '$startDate' and '$endDate' GROUP BY user_id) AS $table");
    }
}
