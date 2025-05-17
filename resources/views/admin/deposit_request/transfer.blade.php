@extends('admin_layouts.app')
\@section('content')

<div class="row justify-content-center">
  <div class="col-lg-12">
    <div class="container mt-2">
      <div class="d-flex justify-content-between">
        <h4>Player Information -- <span>
            Player ID : {{ $cash->user->id }}
          </span></h4>
        <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.user.index') }}">
          <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
        </a>
      </div>
      <div class="card">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
              <tr>
                <th>ID</th>
                <td>{!! $cash->user->id !!}</td>
              </tr>
              <tr>
                <th>User Name</th>
                <td>{!! $cash->user->name !!}</td>
              </tr>
              <tr>
                <th>Phone</th>
                <td>{!! $cash->user->phone !!}</td>
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
            <h5 class="mb-0">Player ထံသို့ ငွေလွဲပေးမည်</h5>

          </div>
        </div>
      </div>
      <div class="card-body">
        
        <form action="{{ route('admin.makeTransfer') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Master Real Name</label>
                <input type="text" class="form-control" name="name" value="{{ $cash->user->name }}" readonly>

              </div>
              @error('name')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="phone" value="{{ $cash->user->phone }}" readonly>

              </div>
              @error('phone')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
          </div>
          <input type="hidden" name="from_user_id" value="{{ Auth::user()->id }}">
          <input type="hidden" name="to_user_id" value="{{ $cash->user_id }}">
          <div class="row">
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <label class="form-label">Player ထံသို့ ငွေလွဲပေးမည့်ပမာဏ</label>
                <input type="decimal" class="form-control" name="amount" value="{{$cash->amount}}">

              </div>
              @error('cash_in')
              <span class="d-block text-danger">*{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline is-valid my-3">
                <select class="form-control" name="p_code" id="choices-code" required>
                  <option value="">Choose Provider Code</option>
                  @foreach ($providers as  $provider)
                    <option value="{{ $provider->p_code }}" >
                    {{ $provider->p_code }}
                    </option>
                    @endforeach
                  </select>
              </div>
              @error('p_code')
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
                <button type="submit" class="btn btn-primary">Player ထံသို့ ငွေလွဲပေးမည်</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection