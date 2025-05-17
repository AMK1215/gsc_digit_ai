@extends('admin_layouts.app')@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <a href="{{ route('admin.player.create') }}" class="btn bg-gradient-primary btn-sm mb-0" style="float: right;">Create Player</a>

        <div class="card-body">
          <h5 class="mb-0">Player Dashboards</h5>

        </div>
        <form action="" method="GET">
          <div class="row mt-3">
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
                <label for="">PlayerId</label>
                <input type="text" class="form-control" name="player_id" value="{{request()->player_id}}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">Phone</label>
                <input type="text" class="form-control" name="phone" value="{{request()->phone}}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">Last Login Ip</label>
                <input type="text" class="form-control" name="last_login_ip" value="{{request()->last_login_ip}}">
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
              <button class="btn btn-sm btn-primary mt-3" id="search" type="submit">Search</button>
              <button class="btn btn-outline-primary btn-sm  mb-0 mt-sm-0" data-type="csv" type="button" name="button" id="export-csv">Export</button>
              <a href="{{route('admin.player.index')}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
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
            @can('master_access')
            <th>AgentName</th>
            @endcan
            <th>Name</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Balance</th>
            <th>RegisterIp</th>
            <th>RegisterTime</th>
            <th>LastLoginIp</th>
            <th>LastLoginTime</th>
            <th>Action</th>
            <th>Transaction</th>
          </thead>
          <tbody>
            @if(isset($users))
            @if(count($users)>0)
            @foreach ($users as $user)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                <span class="d-block">{{ $user->user_name }}</span>
              </td>
              @can('master_access')
              <td>{{$user->parent->name}}</td>
              @endcan
              <td>{{$user->name}}</td>
              <td>{{ $user->phone }}</td>
              <td>
                <small class="badge bg-gradient-{{ $user->status == 1 ? 'success' : 'danger' }}">{{ $user->status == 1 ? "active" : "inactive" }}</small>
              </td>
              <td>{{number_format($user->balanceFloat,2) }} </td>
              <td>{{ $user->userLog->first()->register_ip ?? '' }}</td>
              <td>{{ $user->created_at }}</td>
              <td>{{ $user->userLog->last()->ip_address ?? '' }}</td>
              <td>{{ $user->userLog->last()->created_at ?? '' }}</td>
              <td>
                @if ($user->status == 1)
                <a onclick="event.preventDefault(); document.getElementById('banUser-{{ $user->id }}').submit();" class="me-2" href="#" data-bs-toggle="tooltip" data-bs-original-title="Active Player">
                  <i class="fas fa-user-check text-success" style="font-size: 20px;"></i>
                </a>
                @else
                <a onclick="event.preventDefault(); document.getElementById('banUser-{{ $user->id }}').submit();" class="me-2" href="#" data-bs-toggle="tooltip" data-bs-original-title="InActive Player">
                  <i class="fas fa-user-slash text-danger" style="font-size: 20px;"></i>
                </a>
                @endif
                <form class="d-none" id="banUser-{{ $user->id }}" action="{{ route('admin.player.ban', $user->id) }}" method="post">
                  @csrf
                  @method('PUT')
                </form>
                <a class="me-1" href="{{ route('admin.player.getChangePassword', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Change Password">
                  <i class="fas fa-lock text-info" style="font-size: 20px;"></i>
                </a>
                <a class="me-1" href="{{ route('admin.player.edit', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Player">
                  <i class="fas fa-pen-to-square text-info" style="font-size: 20px;"></i>
                </a>
              </td>
              <td>

                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                data-bs-target="#cashInModal{{ $user->id }}">
                + Deposit
            </button>

            <div class="modal fade" id="cashInModal{{ $user->id }}" tabindex="-1"
                aria-labelledby="cashInModalLabel" aria-hidden="true"
                style="z-index: 1050 !important;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="cashInModalLabel{{ $user->id }}">Deposit To
                                Player</h5>
                            <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form
                                action="{{ route('admin.player.getCashIn', $user->id) }}"
                                method="POST">
                                @csrf

                                <div
                                    class="input-group input-group-outline is-valid my-3">
                                    <label class="form-label mb-2">Amount</label>
                                    <input type="text" class="form-control"
                                        name="amount" required>
                                </div>
                                @error('amount')
                                    <span
                                        class="d-block text-danger">*{{ $message }}</span>
                                @enderror


                                <div
                                    class="input-group input-group-outline is-valid my-3">
                                    <label class="form-label mb-2">Addition Note
                                        (optional)</label>
                                    <input type="text" class="form-control"
                                        name="note">
                                </div>
                                @error('note')
                                    <span
                                        class="d-block text-danger">*{{ $message }}</span>
                                @enderror

                                <div class="text-center"><button
                                        class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




                <button type="button" class="btn btn-info btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#cashOutModal{{ $user->id }}">
                    - Withdraw
                </button>

                <div class="modal fade" id="cashOutModal{{ $user->id }}"
                    tabindex="-1" aria-labelledby="cashOutModalLabel"
                    aria-hidden="true" style="z-index: 1050 !important;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"
                                    id="cashOutModalLabel{{ $user->id }}">Withdraw
                                    To
                                    Player</h5>
                                <button type="button" class="btn-close"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form
                                    action="{{ route('admin.player.makeCashOut', $user->id) }}"
                                    method="POST">
                                    @csrf

                                    <div
                                        class="input-group input-group-outline is-valid my-3">
                                        <label class="form-label mb-2">Amount</label>
                                        <input type="text" class="form-control"
                                            name="amount" required>
                                    </div>
                                    @error('amount')
                                        <span
                                            class="d-block text-danger">*{{ $message }}</span>
                                    @enderror


                                    <div
                                        class="input-group input-group-outline is-valid my-3">
                                        <label class="form-label mb-2">Addition Note
                                            (optional)</label>
                                        <input type="text" class="form-control"
                                            name="note">
                                    </div>
                                    @error('note')
                                        <span
                                            class="d-block text-danger">*{{ $message }}</span>
                                    @enderror

                                    <div class="text-center"><button
                                            class="btn btn-sm btn-success">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.logs', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Reports" class="btn btn-info btn-sm">
                  <i class="fas fa-right-left text-white me-1"></i>
                  Logs
                </a>
              </td>
            </tr>
            @endforeach
            @else
            <tr>
              <td col-span=8>
                There was no Players.
              </td>
            </tr>
            @endif
            @endif

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

    document.getElementById('export-csv').addEventListener('click', function() {
      dataTableSearch.export({
        type: "csv",
        filename: "player_list",
      });
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
