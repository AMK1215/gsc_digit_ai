@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-lg-12">
    <div class=" mt-2">
      <div class="d-flex justify-content-between">


        <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.agent.index') }}">
          <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
        </a>
      </div>
      <div class="card">
        <h4 class="ms-3">Agent Information
        </h4>
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
              <tr>
                <th>User Name</th>
                <td>{!! $agent->name ?? "" !!}</td>
              </tr>
              <tr>
                <th>Phone</th>
                <td>{!! $agent->phone !!}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


</div>
<div class="row mt-4">
  <div class="col-lg-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Withdraw</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">


            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.agent.makeCashOut', $agent->id) }}" method="POST">
          @csrf

          <div class="row">
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label"> Name</label>
                <input type="text" class="form-control" name="name" value="{{ $agent->name }}" readonly>

              </div>
              @error('name')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Current Balance</label>
                <input type="text" class="form-control" name="phone" value="{{ $agent->balanceFloat}}" readonly>

              </div>
              @error('phone')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
          </div>
          <input type="hidden" name="from_user_id" value="{{ $agent->id }}">

          <div class="row">
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Amount</label>
                <input type="text" class="form-control" name="amount" required>
              </div>
              @error('amount')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Addition Note (optional)</label>
                <input type="text" class="form-control" name="note">

              </div>
              @error('note')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
          </div>
          {{-- submit button --}}
          <div class="row">
            <div class="col-md-12">
              <div class="input-group input-group-outline is-valid my-3">
                <button type="submit" class="btn btn-primary">confirm</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var errorMessage = @json(session('error'));
    var successMessage = @json(session('success'));
    console.log(successMessage);
    @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: successMessage,
      timer: 3000,
      showConfirmButton: false
    });
    @elseif(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: errorMessage,
      timer: 3000,
      showConfirmButton: false
    });
    @endif
  });
</script>
<script>
  if (document.getElementById('choices-tags-edit')) {
    var tags = document.getElementById('choices-tags-edit');
    const examples = new Choices(tags, {
      removeItemButton: true
    });
  }
</script>
<script>
  var d = new Date();
  var date = d.getDate();
  var month = d.getMonth() + 1;
  var year = d.getFullYear();
  var output = date + '-' + (month < 10 ? '0' : '') + month + '-' + year;
  document.getElementById('current_date').innerHTML = output;
</script>
@endsection