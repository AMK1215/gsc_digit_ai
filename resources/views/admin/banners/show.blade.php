@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-10">
    <div class="container mt-0">
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5>Banner's Detail</h5>
            <div>
              <a class="btn btn-sm btn-primary" href="{{ route('admin.banners.index') }}">
                <i class="material-icons">arrow_back</i>Back
              </a>
            </div>
          </div>
          <div class="card">
            <div>
              <img src="{{ $banner->img_url }}" class="card-img-top" alt="">
            </div>
            <div class="px-3 pt-2">
                <p class="fw-bold">{{ $banner->agent->name }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
