@extends('admin_layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">

                    <div class="card-body">
                        <h5 class="mb-0">Win/Lose Report</h5>
                    </div>
                    <form action="{{ route('admin.report.index') }}" method="GET">
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="input-group input-group-static mb-4">
                                    <label for="">PlayerId</label>
                                    <input type="text" class="form-control" name="player_id"
                                        value="{{ request()->player_id }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-static mb-4">
                                    <label for="">StartDate</label>
                                    <input type="datetime" class="form-control" name="start_date"
                                        value="{{ request()->get('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-static mb-4">
                                    <label for="">EndDate</label>
                                    <input type="datetime" class="form-control" name="end_date"
                                        value="{{ request()->get('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-sm btn-primary" id="search" type="submit">Search</button>
                                <a href="{{ route('admin.report.index') }}"
                                    class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-icons text-lg mt-0">refresh</i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-flush" id="users-search">
                        <thead class="text-center">
                            <tr>
                                <th>AgentName</th>
                                <th>UserName</th>
                                <th>TotalStake</th>
                                <th>TotalBet</th>
                                <th>TotalWin</th>
                                <th>TotalNetWin</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                            @foreach ($report as $row)
                                <tr>
                                    <td>{{ $row->parent_member_name }}</td>
                                    <td>{{ $row->user_name }}</td>
                                    <td>{{ $row->total_count }}</td>
                                    <td class="">
                                        {{ number_format($row->total_bet_amount, 2) }}</td>
                                    <td class="">
                                        {{ number_format($row->total_payout_amount, 2) }}</td>
                                    <?php
                                    $net_win = $row->total_payout_amount - $row->total_bet_amount;
                                    ?>
                                    <td class="{{ $net_win >= 0 ? 'text-success' : 'text-danger' }}">

                                        {{ number_format($net_win, 2) }}</td>
                                    <td><a href="{{ route('admin.reports.details', $row->user_name) }}">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="text-center">
                            <th></th>
                            <th>Total Stake</th>
                            <th>{{ $total['totalstake'] }}</th>
                            <th>Total Bet Amt</th>
                            <th>{{ $total['totalBetAmt'] }}</th>
                            <th>Total Win Amt</th>
                            <th>{{ $total['totalWinAmt'] }}</th>

                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        if (document.getElementById('users-search')) {
            const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
                searchable: true,
                fixedHeight: false,
                perPage: 7
            });

        };
    </script>
@endsection
