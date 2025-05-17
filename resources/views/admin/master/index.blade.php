@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Master List Dashboards</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">
              <a href="{{ route('admin.master.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Create
              Master</a>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="users-search">
          <thead class="thead-light">
            <th>#</th>
            <th>Master Name</th>
            <th>Master Id</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Balance</th>
            <th>Action</th>
            <th>Transfer</th>
          </thead>
          <tbody>
            {{-- kzt --}}
            @if(isset($users))
            @if(count($users)>0)
            @foreach ($users as $user)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                <span class="d-block">{{ $user->name }}</span>

              </td>
              <td>{{$user->user_name}}</td>
              <td>{{ $user->phone }}</td>
              <td>
              <small class="badge bg-gradient-{{ $user->status == 1 ? 'success' : 'danger' }}">{{ $user->status == 1 ? "active" : "inactive" }}</small>

              </td>
              <td>{{ number_format($user->balanceFloat,2) }} </td>

              <td>
                @if ($user->status == 1)
                <a onclick="event.preventDefault(); document.getElementById('banUser-{{ $user->id }}').submit();" class="me-2" href="#" data-bs-toggle="tooltip" data-bs-original-title="Active Master">
                  <i class="fas fa-user-check text-success" style="font-size: 20px;"></i>
                </a>
                @else
                <a onclick="event.preventDefault(); document.getElementById('banUser-{{ $user->id }}').submit();" class="me-2" href="#" data-bs-toggle="tooltip" data-bs-original-title="InActive Master">
                  <i class="fas fa-user-slash text-danger" style="font-size: 20px;"></i>
                </a>
                @endif
                <form class="d-none" id="banUser-{{ $user->id }}" action="{{ route('admin.master.ban', $user->id) }}" method="post">
                  @csrf
                  @method('PUT')
                </form>
                <a class="me-1" href="{{ route('admin.master.getChangePassword', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Change Password">
                  <i class="fas fa-lock text-info" style="font-size: 20px;"></i>
                </a>
                <a class="me-1" href="{{ route('admin.master.edit', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Agent">
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
                                Master</h5>
                            <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form
                                action="{{ route('admin.master.getCashIn', $user->id) }}"
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
                                    Master</h5>
                                <button type="button" class="btn-close"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form
                                    action="{{ route('admin.master.makeCashOut', $user->id) }}"
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
                <a href="{{ route('admin.logs', $user->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Transfer Logs" class="btn btn-info btn-sm">
                  <i class="fas fa-right-left text-white me-1"></i>
                  Logs
                </a>

              </td>
            </tr>
            @endforeach
            @else
            <tr>
              <td col-span=8>
                There was no Agents.
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

    document.querySelectorAll(".export").forEach(function(el) {
      el.addEventListener("click", function(e) {
        var type = el.dataset.type;

        var data = {
          type: type,
          filename: "material-" + type,
        };

        if (type === "csv") {
          data.columnDelimiter = "|";
        }

        dataTableSearch.export(data);
      });
    });
  };
</script>
@endsection
