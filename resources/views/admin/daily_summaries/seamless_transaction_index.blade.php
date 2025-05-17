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

        .alert {
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">

                    <div class="card-body">
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

                        <!-- Delete by Date Range Form -->
                        @can('owner_access')
                            <form method="POST" action="{{ route('admin.seamless_transactions.delete') }}"
                                class="date-filter-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-danger">Delete Transactions</button>
                                    </div>
                                </div>
                            </form>
                        @endcan

                        <!-- Display AJAX response -->
                        <div id="generationResult" class="mt-3"></div>

                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush" id="users-search">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Transaction ID</th>
                                    <th>Bet Amount</th>
                                    <th>Transaction Amount</th>
                                    <th>Payout Amount</th>
                                    {{-- <th>Status</th> --}}
                                    {{-- <th>Wager Status</th> --}}
                                    <th>Member Name</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transaction->user_id }}</td>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>{{ $transaction->bet_amount }}</td>
                                        <td>{{ $transaction->transaction_amount }}</td>
                                        <td>{{ $transaction->payout_amount }}</td>
                                        {{-- <td>{{ $transaction->status }}</td> --}}
                                        {{-- <td>{{ $transaction->wager_status }}</td> --}}
                                        <td>{{ $transaction->member_name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="pagination">
                            {{ $transactions->links() }}
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
                        <strong>Success!</strong> ${response.message}
                    </div>`;
                        $('#generationResult').html(html);

                        // Reload the page to refresh the table after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        let error = xhr.responseJSON.error || 'An unknown error occurred';
                        let html = `<div class="alert alert-danger">
                        <strong>Error!</strong> ${error}
                    </div>`;
                        $('#generationResult').html(html);
                    }
                });
            });
        </script>
    @endsection
