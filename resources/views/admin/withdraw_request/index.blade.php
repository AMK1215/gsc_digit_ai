@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header pb-0">
                <div class="d-lg-flex">
                    <div>
                        <h5 class="mb-0">WithDrawRequest</h5>
                    </div>
                </div>
                <form action="{{route('admin.agent.withdraw')}}" method="GET">
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">PlayerId</label>
                                <input type="text" class="form-control" name="player_id" value="{{request()->player_id}}">
                            </div>
                        </div>
                        @can('master_access')
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">AgentId</label>
                                <select name="agent_id" class="form-control">
                                    <option value="">Select AgentName</option>
                                    @foreach($agents as $agent)
                                    <option value="{{$agent->id}}" {{request()->agent_id == $agent->id ? 'selected' : ''}}>{{$agent->user_name}}-{{$agent->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endcan
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">Start Date</label>
                                <input type="text" class="form-control" id="datetime" name="start_date" value="{{request()->get('start_date')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">EndDate</label>
                                <input type="text" class="form-control" id="datetime" name="end_date" value="{{request()->get('end_date')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">Status</label>
                                <select name="status" id="" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="0" {{request()->status == "0" ? 'selected' : ''}}>pending</option>
                                    <option value="1" {{request()->status == 1 ? 'selected' : ''}}>approved</option>
                                    <option value="2" {{request()->status == 2 ? 'selected' : ''}}>Reject</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">PaymentType</label>
                                <select name="payment_type_id" id="" class="form-control">
                                    <option value="">Select Status</option>
                                    @foreach($paymentTypes as $type)
                                    <option value="{{$type->id}}" {{request()->payment_type_id == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary mt-3" id="search" type="submit">Search</button>
                            <button class="btn btn-outline-primary btn-sm  mb-0 mt-sm-0" data-type="csv" type="button" name="button" id="export-csv">Export</button>
                            <a href="{{route('admin.agent.withdraw')}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
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
                        <th>PlayerId</th>
                        <th>PlayerName</th>
                        <th>AgentName</th>
                        <th>Requested Amount</th>
                        <th>Before Amount</th>
                        <th>After Amount</th>
                        <th>Payment Method</th>
                        <th>Bank Account Name</th>
                        <th>Bank Account Number</th>
                        <th>Status</th>
                        <th>Created_at</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($withdraws as $withdraw)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{$withdraw->user->user_name}}</td>
                            <td>{{$withdraw->user->name}}</td>
                            <td><span class="badge text-bg-warning text-white ">{{$withdraw->user->parent->name}}</span></td>
                            <td class="amount">{{ number_format($withdraw->amount) }}</td>
                            <td class="amount">{{ number_format($withdraw->before_amount) }}</td>
                            <td class="amount">{{ number_format($withdraw->after_amount) }}</td>
                            <td>{{$withdraw->paymentType->name}}</td>
                            <td>{{$withdraw->account_name}}</td>
                            <td>{{$withdraw->account_no}}</td>
                            <td>
                                @if ($withdraw->status == 0)
                                <span class="badge text-bg-warning text-white mb-2">Pending</span>
                                @elseif ($withdraw->status == 1)
                                <span class="badge text-bg-success text-white mb-2">Approved</span>
                                @elseif ($withdraw->status == 2)
                                <span class="badge text-bg-danger text-white mb-2">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $withdraw->created_at->format('d-m-Y H:i:s') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('admin.agent.withdrawStatusUpdate', $withdraw->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="amount" value="{{ $withdraw->amount }}">
                                        <input type="hidden" name="status" value="1">
                                        <input type="hidden" name="player" value="{{ $withdraw->user_id }}">
                                        @if($withdraw->status == 0)
                                        <button class="btn btn-success p-1 me-1" type="submit">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                    </form>

                                    <form action="{{ route('admin.agent.withdrawStatusreject', $withdraw->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="status" value="2">
                                        @if($withdraw->status == 0)
                                        <button class="btn btn-danger p-1 me-1" type="submit">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                    <tr id="tfoot">
                        <th colspan="4" class="text-center text-dark">Total Amount:</th>
                        <th class="text-dark">{{number_format($totalAmount, 2)}}</th>
                        <th colspan="6"></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('users-search')) {
            const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
                searchable: false,
                fixedHeight: false,
                perPage: 7
            });

            document.getElementById('export-csv').addEventListener('click', function() {
                dataTableSearch.export({
                    type: "csv",
                    filename: "withdraw",
                });
            });
        }
    });
</script>
@endsection