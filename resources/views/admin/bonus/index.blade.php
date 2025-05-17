@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header pb-0">
                <div class="d-lg-flex">
                    <div>
                        <h5 class="mb-0">Bonus List</h5>
                    </div>
                    <div class="ms-auto my-auto mt-lg-0 mt-4">
                        <div class="ms-auto my-auto">
                            <a href="{{ route('admin.bonus.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Create</a>
                        </div>
                    </div>
                </div>
                <form action="{{route('admin.bonus.index')}}" method="GET">
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">Bonus Types</label>
                                <select name="type" class="form-control">
                                    <option value="">Select type</option>
                                    @foreach($bonusTypes as $bonus)
                                    <option value="{{$bonus->id}}" {{request()->type == $bonus->id ? 'selected' : ''}}>{{$bonus->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">PlayerId</label>
                                <input type="text" name="player_id" class="form-control" value="{{request()->get('player_id')}}">
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
                                <input type="datetime" class="form-control" name="start_date" value="{{request()->get('start_date')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="">EndDate</label>
                                <input type="datetime" class="form-control" name="end_date" value="{{request()->get('end_date')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary mt-3" id="search" type="submit">Search</button>
                            <button class="btn btn-outline-primary btn-sm  mb-0 mt-sm-0" data-type="csv" type="button" name="button" id="export-csv">Export</button>
                            <a href="{{route('admin.bonus.index')}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
                                <i class="material-icons text-lg mt-0">refresh</i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-flush" id="banners-search">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>AgentName</th>
                            <th>PlayerId</th>
                            <th>Name</th>
                            <th>BonusType</th>
                            <th>Amount</th>
                            <th>BeforeAmount</th>
                            <th>AfterAmount</th>
                            <th>Remark</th>
                            <th>Time</th>
                            <th>CreatedBy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bonuses as $bonus)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$bonus->user->parent->name}}</td>
                            <td>{{$bonus->user->user_name}}</td>
                            <td>{{$bonus->user->name}}</td>
                            <td>{{$bonus->type->name}}</td>
                            <td>{{$bonus->amount}}</td>
                            <td>{{$bonus->before_amount}}</td>
                            <td>{{$bonus->after_amount}}</td>
                            <td>{{$bonus->remark}}</td>
                            <td>{{$bonus->created_at}}</td>
                            <td>{{$bonus->agent->name}}</td>
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
    if (document.getElementById('banners-search')) {
        const dataTableSearch = new simpleDatatables.DataTable("#banners-search", {
            searchable: true,
            fixedHeight: false,
            perPage: 10
        });

        document.getElementById('export-csv').addEventListener('click', function() {
          dataTableSearch.export({
            type: "csv",
            filename: "bonus",
          });
        });
    };
</script>
<script>
    $(document).ready(function() {
        $('.transparent-btn').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection