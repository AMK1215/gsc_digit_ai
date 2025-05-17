@extends('admin_layouts.app')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">

                <div class="card-body">
                    <h5 class="mb-0">Win/Lose Details</h5>
                </div>
                <form action="{{route('admin.report.detail', $playerId)}}" method="GET">
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">Product Type</label>
                                <select name="product_id" id="" class="form-control">
                                    <option value="">Select Product type</option>
                                    @foreach($productTypes as $type)
                                    <option value="{{$type->id}}" {{ $type->id == request()->product_id ? 'selected' : ''}}>{{$type->provider_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">Start Date</label>
                                <input type="text" class="form-control" name="start_date" value="{{request()->get('start_date')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">EndDate</label>
                                <input type="text" class="form-control" name="end_date" value="{{request()->get('end_date')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary" id="search" type="submit">Search</button>
                            <a href="{{route('admin.report.detail', $playerId)}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
                                <i class="material-icons text-lg mt-0">refresh</i>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary" id="search" type="submit">Search</button>
                            <a href="{{route('admin.report.detail', $playerId)}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
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
                        <th>Product Name</th>
                        <th>Game Name</th>
                        <th>Valid Bet</th>
                        <th>Total Win Amount</th>
                        <th>Win/Lose Amt</th>
                        <th>Created At</th>
                    </thead>
                    <tbody>
                        @foreach($details as $detail)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$detail->user_name}}</td>
                            <td>{{$detail->name}}</td>
                            <td>{{$detail->provider_name}}</td>
                            <td>{{$detail->game_name}}</td>
                            <td>{{number_format($detail->total_bet_amount, 2)}}</td>
                            <td>{{number_format($detail->win_amount, 2)}}</td>
                            <td><span class="{{$detail->net_win > 0 ? 'text-success' : 'text-danger' }}">{{number_format($detail->net_win, 2)}}</span></td>
                            <td>{{$detail->date}}</td>
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
@endsection