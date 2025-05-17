@extends('admin_layouts.app')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.games.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">New Game Create</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.games.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold" for="inputEmail1">Name</label>
                    <input type="text" class="form-control border border-1 border-secondary px-2" id="inputEmail1" name="name" placeholder="Enter Name">
                    @error('name')
                    <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold" for="title">Game Image</label>
                    <input type="file" class="form-control border border-1 border-secondary px-2" id="inputEmail3" name="image">
                    @error('image')
                    <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold" for="inputEmail2">Link</label>
                    <input type="text" class="form-control border border-1 border-secondary px-2" id="inputEmail2" name="link" placeholder="Enter Link">
                    @error('link')
                    <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                </div>
                <div class="">
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

