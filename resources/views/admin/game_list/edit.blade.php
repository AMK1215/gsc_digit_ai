@extends('admin_layouts.app')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.gametypes.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Edit Game Type</h4>
              </div>
            </div>
            <div class="card-body">
            
              <form  action="{{ route('admin.gametypes.update',[$gameType->id,$productId]) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="custom-form-group">
                  <label for="title">GameType</label>
                  <input type="text" class="form-control" value="{{$gameType->name}}" name="payment_method" readonly>
                </div>
                <div class="custom-form-group">
                  <label for="phone">Product</label>
                  <input type="text" class="form-control" value="{{$gameType->products[0]['name']}}" readonly>
                </div>
                <div class="custom-form-group">
                  <label for="name">Image</label>
                  <input type="file" class="form-control" name="image">
                  <img src="{{$gameType->products[0]->getImgUrlAttribute()}}" alt="" width="100px">
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
