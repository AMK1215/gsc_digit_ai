@extends('admin_layouts.app')\
@section('content')
<div class="row mt-4">
  <div class="col-lg-12">
    <div class="card">
      <!-- Card header -->
      
      <div class="card-body">
        <form action="{{ route('admin.agent.statusChange',$withdraw->id) }}" method="POST">
          @csrf
          <div class="row">
          <input type="text" class="form-control" name="player" value="{{ $withdraw->user->id }}" readonly>

            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">User Name</label>
                <input type="text" class="form-control" name="name" value="{{ $withdraw->user->name }}" readonly>

              </div>
              @error('name')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="{{ $withdraw->user->phone }}" readonly>

              </div>
            </div>

            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Bank Account Name</label>
                <input type="text" class="form-control" name="account_name" value="{{ $withdraw->account_name }}" readonly>

              </div>
            </div>

            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Bank Account No</label>
                <input type="text" class="form-control" name="account_no" value="{{ $withdraw->account_no }}" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Payment Method</label>
                <input type="text" class="form-control" name="" value="{{ $withdraw->paymentType->name }}" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Amount</label>
                <input type="text" class="form-control" name="amount" value="{{ $withdraw->amount }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <select name="status" id="" class="form-control">
                <option value="0" {{ $withdraw->status == 0 ? 'selected' : '' }}>Pending</option>
                <option value="1" {{ $withdraw->status == 1 ? 'selected' : '' }}>Approved</option>
                <option value="2" {{ $withdraw->status == 2 ? 'selected' : '' }}>Rejected</option>

                </select>
                @error('status')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="input-group input-group-outline is-valid my-3">
                <button type="submit" class="btn btn-primary">Player ထံမှ ငွေထုတ်ယူမည်</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection