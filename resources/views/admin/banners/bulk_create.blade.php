@extends('admin_layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.banners.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Banner Bulk Create (For My Agents)</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.banners.bulkStore') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="custom-form-group">
                  <label for="title">Mobile Image<span class="text-danger">*</span></label>
                  <input type="file" class="form-control" id="mobile_image" name="mobile_image">
                  @error('mobile_image')
                    <span class="text-danger">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="custom-form-group">
                  <label for="title">Desktop Image<span class="text-danger">*</span></label>
                  <input type="file" class="form-control" id="" name="desktop_image">
                  @error('desktop_image')
                    <span class="text-danger">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="custom-form-group">
                  <button class="btn btn-primary" type="submit">Create</button>
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

