<?php

namespace App\Http\Controllers;

use App\Models\Admin\BetresultBackup;
use App\Models\Admin\Product;
use App\Models\Admin\ResultBackup;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportV2Controller extends Controller
{
    private $carbon;

    public function __construct(Carbon $carbon)
    {
        $this->carbon = $carbon;
    }

    public function index(Request $request)
    {
        $adminId = auth()->id();

        $results = $this->buildQuery($request, $adminId)->get();

        return view('report.v2.index', compact('results'));
    }

    public function detail(Request $request, $playerId)
    {
        $details = $this->getPlayerDetails($playerId, $request);

        $productTypes = Product::where('is_active', 1)->get();

        return view('report.v2.detail', compact('details', 'productTypes', 'playerId'));
    }

    private function buildQuery(Request $request, $adminId)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

        $resultsSubquery = ResultBackup::select(
            'result_backups.user_id',
            DB::raw('SUM(result_backups.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(result_backups.win_amount) as win_amount'),
            DB::raw('SUM(result_backups.net_win) as net_win')
        )
            ->groupBy('result_backups.user_id')
            ->whereBetween('result_backups.created_at', [$startDate, $endDate]);

        $betsSubquery = BetresultBackup::select(
            'betresult_backups.user_id',
            DB::raw('SUM(betresult_backups.bet_amount) as bet_total_bet_amount'),
            DB::raw('SUM(betresult_backups.win_amount) as bet_total_win_amount'),
            DB::raw('SUM(betresult_backups.net_win) as bet_total_net_amount')
        )
            ->groupBy('betresult_backups.user_id')
            ->whereBetween('betresult_backups.created_at', [$startDate, $endDate]);

        $query = DB::table('users as players')
            ->select(
                'players.id as user_id',
                'players.name as player_name',
                'players.user_name as user_name',
                'agents.name as agent_name',
                DB::raw('IFNULL(result_backups.total_bet_amount, 0) + IFNULL(bets.bet_total_bet_amount, 0) as total_bet_amount'),
                DB::raw('IFNULL(result_backups.win_amount, 0) + IFNULL(bets.bet_total_win_amount, 0) as total_win_amount'),
                DB::raw('IFNULL(result_backups.net_win, 0) + IFNULL(bets.bet_total_net_amount, 0) as total_net_win'),
                DB::raw('MAX(wallets.balance) as balance'),
                DB::raw('IFNULL(deposit_requests.total_amount, 0) as deposit_amount'),
                DB::raw('IFNULL(with_draw_requests.total_amount, 0) as withdraw_amount'),
                DB::raw('IFNULL(bonuses.total_amount, 0) as bonus_amount')
            )
            ->leftJoin('users as agents', 'players.agent_id', '=', 'agents.id')
            ->leftJoin('wallets', 'wallets.holder_id', '=', 'players.id')
            ->leftJoinSub($resultsSubquery, 'result_backups', 'result_backups.user_id', '=', 'players.id') // Fixed alias
            ->leftJoinSub($betsSubquery, 'bets', 'bets.user_id', '=', 'players.id') // Fixed alias
            ->leftJoin($this->getSubquery('bonuses'), 'bonuses.user_id', '=', 'players.id')
            ->leftJoin($this->getSubquery('deposit_requests', 'status = 1'), 'deposit_requests.user_id', '=', 'players.id')
            ->leftJoin($this->getSubquery('with_draw_requests', 'status = 1'), 'with_draw_requests.user_id', '=', 'players.id')
            ->when($request->player_id, fn ($query) => $query->where('players.user_name', $request->player_id))
            ->where(function ($query) {
                $query->whereNotNull('result_backups.user_id')
                    ->orWhereNotNull('bets.user_id');
            });

        $this->applyRoleFilter($query, $adminId);

        return $query->groupBy('players.id', 'players.name', 'players.user_name', 'agents.name');
    }

    private function applyRoleFilter($query, $adminId)
    {
        if (Auth::user()->hasRole('Owner')) {
            $query->where('agents.agent_id', $adminId);
        } elseif (Auth::user()->hasRole('Agent')) {
            $query->where('agents.id', $adminId);
        }

    }

    private function getPlayerDetails($playerId, $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d H:i') : Carbon::today()->startOfMonth()->format('Y-m-d H:i');
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d H:i') : Carbon::today()->endOfMonth()->format('Y-m-d H:i');

        $combinedSubquery = DB::table('result_backups')
            ->select(
                'user_id',
                'total_bet_amount',
                'win_amount',
                'net_win',
                'game_lists.game_name',
                'products.provider_name',
                'result_backups.created_at as date'
            )
            ->join('game_lists', 'game_lists.game_id', '=', 'result_backups.game_code')
            ->join('products', 'products.id', '=', 'game_lists.product_id')
            ->whereBetween('result_backups.created_at', [$startDate, $endDate])
            ->when($request->product_id, fn ($query) => $query->where('products.id', $request->product_id))
            ->unionAll(
                DB::table('betresult_backups')
                    ->select(
                        'user_id',
                        'bet_amount as total_bet_amount',
                        'win_amount',
                        'net_win',
                        'game_lists.game_name',
                        'products.provider_name',
                        'betresult_backups.created_at as date'
                    )
                    ->join('game_lists', 'game_lists.game_id', '=', 'betresult_backups.game_code')
                    ->join('products', 'products.id', '=', 'game_lists.product_id')
                    ->whereBetween('betresult_backups.created_at', [$startDate, $endDate])
                    ->when($request->product_id, fn ($query) => $query->where('products.id', $request->product_id))
            );

        $query = DB::table('users as players')
            ->joinSub($combinedSubquery, 'combined', 'combined.user_id', '=', 'players.id')
            ->where('players.id', $playerId);

        return $query->orderBy('date', 'desc')->get();
    }

    private function getSubquery($table, $condition = '1=1')
    {
        return DB::raw("(SELECT user_id, SUM(amount) AS total_amount FROM $table WHERE $condition GROUP BY user_id) AS $table");
    }
}
// class ReportV2Controller extends Controller
// {
//     public function index(Request $request)
//     {
//         $adminId = auth()->id();
//         $results = $this->buildQuery($request, $adminId)->get();

//         return view('report.v2.index', compact('results'));
//     }

//     public function detail(Request $request, $playerId)
//     {
//         $details = $this->getPlayerDetails($playerId, $request);
//         $productTypes = Product::active()->get();

//         return view('report.v2.detail', compact('details', 'productTypes', 'playerId'));
//     }

//     private function buildQuery(Request $request, $adminId)
//     {
//         [$startDate, $endDate] = $this->getDateRange($request);

//         $resultsSubquery = $this->getSubqueryAggregate('result_backups', ['total_bet_amount', 'win_amount', 'net_win'], $startDate, $endDate);
//         $betsSubquery = $this->getSubqueryAggregate('betresult_backups', ['bet_amount as bet_total_bet_amount', 'win_amount as bet_total_win_amount', 'net_win as bet_total_net_amount'], $startDate, $endDate);

//         $query = DB::table('users as players')
//             ->select(
//                 'players.id as user_id',
//                 'players.name as player_name',
//                 'players.user_name',
//                 'agents.name as agent_name',
//                 DB::raw('COALESCE(result_backups.total_bet_amount, 0) + COALESCE(bets.bet_total_bet_amount, 0) as total_bet_amount'),
//                 DB::raw('COALESCE(result_backups.win_amount, 0) + COALESCE(bets.bet_total_win_amount, 0) as total_win_amount'),
//                 DB::raw('COALESCE(result_backups.net_win, 0) + COALESCE(bets.bet_total_net_amount, 0) as total_net_win'),
//                 DB::raw('MAX(wallets.balance) as balance'),
//                 DB::raw('COALESCE(deposit_requests.total_amount, 0) as deposit_amount'),
//                 DB::raw('COALESCE(with_draw_requests.total_amount, 0) as withdraw_amount'),
//                 DB::raw('COALESCE(bonuses.total_amount, 0) as bonus_amount')
//             )
//             ->leftJoin('users as agents', 'players.agent_id', '=', 'agents.id')
//             ->leftJoin('wallets', 'wallets.holder_id', '=', 'players.id')
//             ->leftJoinSub($resultsSubquery, 'result_backups', 'result_backups.user_id', '=', 'players.id')
//             ->leftJoinSub($betsSubquery, 'bets', 'bets.user_id', '=', 'players.id')
//             ->leftJoinSub($this->getSubqueryAggregate('bonuses'), 'bonuses', 'bonuses.user_id', '=', 'players.id')
//             ->leftJoinSub($this->getSubqueryAggregate('deposit_requests', 'status = 1'), 'deposit_requests', 'deposit_requests.user_id', '=', 'players.id')
//             ->leftJoinSub($this->getSubqueryAggregate('with_draw_requests', 'status = 1'), 'with_draw_requests', 'with_draw_requests.user_id', '=', 'players.id')
//             ->when($request->player_id, fn($query) => $query->where('players.user_name', $request->player_id))
//             ->where(function ($query) {
//                 $query->whereNotNull('result_backups.user_id')
//                       ->orWhereNotNull('bets.user_id');
//             });

//         $this->applyRoleFilter($query, $adminId);

//         return $query->groupBy('players.id', 'players.name', 'players.user_name', 'agents.name');
//     }

//     private function applyRoleFilter($query, $adminId)
//     {
//         $role = Auth::user()->getRole();
//         if ($role === 'Master') {
//             $query->where('agents.agent_id', $adminId);
//         } elseif ($role === 'Agent') {
//             $query->where('agents.id', $adminId);
//         }
//     }

//     private function getPlayerDetails($playerId, Request $request)
//     {
//         [$startDate, $endDate] = $this->getDateRange($request);

//         $combinedSubquery = DB::table('result_backups')
//             ->select(
//                 'user_id',
//                 'total_bet_amount',
//                 'win_amount',
//                 'net_win',
//                 'game_lists.game_name',
//                 'products.provider_name',
//                 'result_backups.created_at as date'
//             )
//             ->join('game_lists', 'game_lists.game_id', '=', 'result_backups.game_code')
//             ->join('products', 'products.id', '=', 'game_lists.product_id')
//             ->whereBetween('result_backups.created_at', [$startDate, $endDate])
//             ->when($request->product_id, fn($query) => $query->where('products.id', $request->product_id))
//             ->unionAll(
//                 DB::table('betresult_backups')
//                     ->select(
//                         'user_id',
//                         'bet_amount as total_bet_amount',
//                         'win_amount',
//                         'net_win',
//                         'game_lists.game_name',
//                         'products.provider_name',
//                         'betresult_backups.created_at as date'
//                     )
//                     ->join('game_lists', 'game_lists.game_id', '=', 'betresult_backups.game_code')
//                     ->join('products', 'products.id', '=', 'game_lists.product_id')
//                     ->whereBetween('betresult_backups.created_at', [$startDate, $endDate])
//                     ->when($request->product_id, fn($query) => $query->where('products.id', $request->product_id))
//             );

//         return DB::table('users as players')
//             ->joinSub($combinedSubquery, 'combined', 'combined.user_id', '=', 'players.id')
//             ->where('players.id', $playerId)
//             ->orderByDesc('date')
//             ->get();
//     }

//     private function getDateRange(Request $request): array
//     {
//         $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i') : Carbon::today()->startOfDay()->format('Y-m-d H:i');
//         $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i') : Carbon::today()->endOfDay()->format('Y-m-d H:i');

//         return [$startDate, $endDate];
//     }

//     private function getSubqueryAggregate(string $table, $columns = [], string $condition = '1=1')
//     {
//         $selectColumns = collect($columns)->map(fn($col) => "SUM($col) AS total_$col")->join(', ');
//         return DB::raw("(SELECT user_id, $selectColumns FROM $table WHERE $condition GROUP BY user_id) AS $table");
//     }
// }
