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
              <h5 class="mb-0">Edit User</h5>

            </div>
            <div class="ms-auto my-auto mt-lg-0 mt-4">
              <div class="ms-auto my-auto">
                <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.player.index') }}">
                  <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
                </a>

              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <form role="form" class="text-start" method='post' action="{{route('admin.player.update',$player->id)}}">
            @csrf
            @method('PUT')
            <div class="custom-form-group">
              <label for="title">PlayerName <span class="text-danger">*</span></label>
              <input type="text" name="user_name" class="form-control" value="{{$player->user_name}}" readonly>
              @error('user_name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{{$player->name}}">
              @error('name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Phone No <span class="text-danger">*</span></label>
              <input type="text" name="phone" class="form-control" value="{{$player->phone}}">
              @error('phone')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
          
            <div class="custom-form-group">
              <button class="btn btn-primary" type="submit">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection
@section('scripts')
<script>
  var errorMessage = @json(session('error'));
  var successMessage = @json(session('success'));
  @if(session() -> has('success'))
  Swal.fire({
    title: successMessage,
    icon: "success",
    showConfirmButton: false,
    showCloseButton: true,
    
  });
  @elseif(session()->has('error'))
  Swal.fire({
    icon: 'error',
    title: errorMessage,
    showConfirmButton: false,
    timer: 1500
  })
  @endif
</script>
@endsection