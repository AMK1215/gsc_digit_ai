@extends('admin_layouts.app')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.products.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">New Product</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label class="form-label text-dark fw-bold" for="inputEmail1">Name<span class="text-danger">*</span></label>
                  <input type="name" class="form-control border border-1 border-secondary px-2" id="inputEmail1" name="name" placeholder="Enter Product Name" value="{{old('name')}}">
                  @error('name')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="custom-form-group">
                  <label for="title">GameType <span class="text-danger">*</span></label>
                  <div class="custom-select-wrapper">
                    <select class="form-control" name="game_type_id[]" id="choices-tags-edit" multiple>
                      @foreach ($gameTypes as $type)
                      <option value="{{$type->id}}">{{$type->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  @error('game_type_id')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="mb-3">
                  <label class="form-label text-dark fw-bold" for="inputEmail1">Product Code<span class="text-danger">*</span></label>
                  <input type="text" class="form-control border border-1 border-secondary px-2" id="inputEmail1" name="code" placeholder="Enter Product Code" value="{{old('code')}}">
                  @error('code')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="mb-3">
                  <label class="form-label text-dark fw-bold" for="inputEmail1">Image<span class="text-danger">*</span></label>
                  <input type="file" class="form-control border border-1 border-secondary px-2" id="inputEmail1" name="image">
                  @error('image')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="mb-3">
                  <label class="form-label text-dark fw-bold" for="inputEmail1">Order</label>
                  <input type="text" class="form-control border border-1 border-secondary px-2" id="inputEmail1" name="order" placeholder="Enter Order" value="{{old('order')}}">
                  @error('order')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="custom-form-group">
                  <label for="title">Rate</label>
                  <input type="text" name="rate" class="form-control" value="{{old('rate')}}" placeholder="0.00">
                  @error('rate')
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
