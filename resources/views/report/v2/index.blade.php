@extends('admin_layouts.app')

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">

                    <div class="card-body">
                        <h5 class="mb-0">V2-Win/Lose Report</h5>
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
                                <a href="{{ route('admin.reportv2.index') }}"
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
                        <thead class="thead-light">
                            <th>#</th>
                            <th>PlayerID</th>
                            <th>Name</th>
                            <th>Agent</th>
                            <th>Account Balance</th>
                            <th>Deposit</th>
                            <th>Withdraw</th>
                            <th>Bonus Amt</th>
                            <th>Valid Bet</th>
                            <th>Win/Lose Amt</th>
                            <th>Profit & loss Amt</th>
                            <th>Detail</th>
                        </thead>
                        <tbody>
                            @foreach ($results as $result)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="d-block">{{ $result->user_name }}</span>
                                    </td>
                                    <td>{{ $result->player_name }}</td>
                                    <td>{{ $result->agent_name }}</td>
                                    <td>{{ number_format($result->balance / 100, 2) }} </td>
                                    <td>{{ number_format($result->deposit_amount, 2) }}</td>
                                    <td>{{ number_format($result->withdraw_amount, 2) }}</td>
                                    <td>{{ $result->bonus_amount }}</td>
                                    <td>{{ number_format($result->total_bet_amount, 2) }}</td>
                                    <td> <span
                                            class="{{ $result->total_net_win > 1 ? 'text-success' : 'text-danger' }}">{{ number_format($result->total_net_win, 2) }}</span>
                                    </td>
                                    <?php
                                    $profit = $result->total_net_win + $result->bonus_amount;
                                    ?>
                                    <td> <span
                                            class="{{ $profit > 1 ? 'text-success' : 'text-danger' }}">{{ number_format($profit, 2) }}</span>
                                    </td>
                                    <td><a href="{{ route('admin.reportv2.detail', $result->user_id) }}"
                                            class="btn btn-primary">Detail</a></td>
                                </tr>
                            @endforeach


                        </tbody>
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
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
