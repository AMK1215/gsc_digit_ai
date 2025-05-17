@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
 <div class="col-lg-12">
  <div class=" mt-2">
   <div class="d-flex justify-content-between">
    <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.master.index') }}">
     <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
    </a>
   </div>
   <div class="card">
   <h4 class="ms-3">Master Information </h4>
    <div class="table-responsive">
    <table class="table align-items-center mb-0">
            <tbody>
              <tr>
                <th>User Name</th>
                <td>{!! $master->name ?? "" !!}</td>
              </tr>
              <tr>
                <th>Phone</th>
                <td>{!! $master->phone !!}</td>
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
       <h5 class="mb-0">Deposit</h5>
      </div>
      <div class="ms-auto my-auto mt-lg-0 mt-4">
       <div class="ms-auto my-auto">
        

       </div>
      </div>
     </div>
    </div>
    <div class="card-body">
    <form action="{{ route('admin.master.makeCashIn',$master->id) }}" method="POST">
      @csrf
  <div class="row">
    <div class="col-md-6">
      <div class="input-group input-group-outline is-valid my-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="{{ $master->name }}" readonly>

      </div>
      @error('name')
        <span class="d-block text-danger">*{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6">
      <div class="input-group input-group-outline is-valid my-3">
        <label class="form-label">Current Balance</label>
        <input type="text" class="form-control" name="blance" value="{{ $master->balanceFloat}}" readonly>

      </div>
      @error('phone')
        <span class="d-block text-danger">*{{ $message }}</span>
        @enderror
    </div>
  </div>
  <input type="hidden" name="from_user_id" value="{{ Auth::id() }}">
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
        <button type="submit" class="btn btn-primary">Confirm</button>
      </div>
    </div>
  </div>
</form>
    </div>
   </div>
 </div>
</div>


@endsection