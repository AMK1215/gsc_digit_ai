@extends('admin_layouts.app')
@section('content')
<div class="container text-center mt-4">
  <div class="row">
    <div class="col-12 col-md-8 mx-auto">
      <div class="card">
        <!-- Card header -->
        <div class="card-header pb-0">
          <div class="d-lg-flex">
            <div>
              <h5 class="mb-0">Edit Agent</h5>

            </div>
            <div class="ms-auto my-auto mt-lg-0 mt-4">
              <div class="ms-auto my-auto">
                <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.agent.index') }}">
                  <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <form role="form" method="POST" class="text-start" action="{{ route('admin.agent.update',$agent->id) }}">
            @csrf
            @method('PUT')
            <div class="custom-form-group">
              <label for="title">Agent Id <span class="text-danger">*</span></label>
              <input type="text"  name="user_name" class="form-control" value="{{$agent->user_name}}" readonly>
              @error('name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">ReferralCode<span class="text-danger">*</span></label>
              <input type="text"  name="referral_code" class="form-control" value="{{$agent->referral_code}}">
              @error('referral_code')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Agent Name <span class="text-danger">*</span></label>
              <input type="text"  name="name" class="form-control" value="{{$agent->name}}">
              @error('name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Phone No</label>
              <input type="text"  name="phone" class="form-control" value="{{$agent->phone}}">
              @error('phone')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <button type="submit" class="btn btn-primary" type="button">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection