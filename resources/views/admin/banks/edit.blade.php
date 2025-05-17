@extends('admin_layouts.app')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.banks.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Edit Bank</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.banks.update', $bank->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="custom-form-group">
                  <label for="title">Bank Type <span class="text-danger">*</span></label>
                  <div class="custom-select-wrapper">
                    <select name="payment_type_id" class="form-control custom-select">
                      @foreach ($payment_types as $type)
                      <option value="{{ $type->id}}"
                        {{ $bank->payment_type_id == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="custom-form-group">
                    <label for="title">Account Name</label>
                    <input type="text" class="form-control" id="" name="account_name" value="{{$bank->account_name}}">
                    @error('account_name')
                    <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="custom-form-group">
                    <label for="title">Account No</label>
                    <input type="text" class="form-control" id="" name="account_number" value="{{$bank->account_number}}">
                    @error('account_number')
                    <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                  </div>
                  @if(Auth::user()->hasRole('Master'))
                  <div class="mb-3">
                    <div class="d-flex">
                      <div class="me-2 single" id="single">
                        <label for="single" class="form-label">
                          <input type="radio"
                            name="type"
                            value="single"
                            class=" me-2"
                            id="single" {{$bank->bankAgents->count() == 1 ? 'checked' : '' }}>
                          Single
                        </label>
                      </div>
                      <div class="me-2">
                        <label for="all" class="form-label">
                          <input type="radio"
                            name="type"
                            value="all"
                            class=" me-2"
                            id="all" {{$bank->bankAgents->count() > 1 ? 'checked' : '' }}>
                          All
                        </label>
                      </div>
                    </div>
                    @error('type')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="custom-form-group {{$bank->bankAgents->count() > 1 ? 'is-hide' : '' }} " id="singleAgent">
                    <label for="title">Select Agent</label>
                    <select name="agent_id" class="form-control form-select" id="">
                      @foreach (Auth::user()->agents as $agent)
                      <option
                        value="{{ $agent->id }}"
                        {{ $bank->bankAgents->contains('agent_id', $agent->id) ? 'selected' : '' }}>
                        {{ $agent->name }}
                      </option> @endforeach
                    </select>
                    @error('agent_id')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  @endif
                  <div class="custom-form-group">
                    <button class="btn btn-primary" type="submit">Edit</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
  $(document).ready(function() {
    $(".is-hide").hide();
    $("#single").on("change", function() {
      console.log('here');
      $("#singleAgent").show();
    });
    $("#all").on("change", function() {
      $("#singleAgent").hide();
    });
  });
</script>
@endsection