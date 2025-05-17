@extends('admin_layouts.app')

@section('styles')
    <style>
        .pagination {
            margin: 20px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
        }

        .date-filter-form {
            margin-bottom: 20px;
        }

        .date-filter-form .form-group {
            margin-right: 15px;
            display: inline-block;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">

                    <div class="card-body">
                        <h5 class="mb-0">Daily Total Report</h5>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    @can('owner_access')
                        <form method="POST" action="{{ route('admin.generate_daily_sammary') }}" class="date-filter-form">
                            @csrf
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="input-group input-group-static mb-4">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-static mb-4">
                                        <label for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-static mb-4">
                                        <button type="submit" class="btn btn-primary">Generate Summaries</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endcan

                    <div id="generationResult" class="mt-3"></div>

                </div>
                <div class="table-responsive">
                    <table class="table table-flush" id="users-search">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Member Name</th>
                                <th>Agent ID</th>
                                <th>Valid Bet Amount</th>
                                <th>Payout Amount</th>
                                <th>Total Bet Amount</th>
                                <th>Win Amount</th>
                                <th>Lose Amount</th>
                                <th>Stake Count</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($summaries as $summary)
                                <tr>
                                    <td>{{ $summary->report_date_formatted }}</td>
                                    <td>{{ $summary->member_name ?? 'N/A' }}</td>
                                    <td>{{ $summary->agent_id ?? 'N/A' }}</td>
                                    <td>{{ number_format($summary->total_valid_bet_amount ?? 0) }}</td>
                                    <td>{{ number_format($summary->total_payout_amount) }}</td>
                                    <td>{{ number_format($summary->total_bet_amount) }}</td>
                                    <td>{{ number_format($summary->total_win_amount) }}</td>
                                    <td>{{ number_format($summary->total_lose_amount) }}</td>
                                    <td>{{ $summary->total_stake_count }}</td>
                                    <td>{{ $summary->created_at_formatted }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">No summaries found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pagination">
                        {{ $summaries->links() }}
                    </div>
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
        // Handle form submission with AJAX
        $('.date-filter-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    let html = `<div class="alert alert-success">
                <strong>Success!</strong> ${response.message}<br>
                Processed dates: ${response.processed_dates.join(', ')}<br>
                Total summaries created: ${response.total_summaries_created}
            </div>`;
                    $('#generationResult').html(html);
                },
                error: function(xhr) {
                    let error = xhr.responseJSON.error || 'Unknown error occurred';
                    $('#generationResult').html(`<div class="alert alert-danger">${error}</div>`);
                }
            });
        });
    </script>
@endsection
