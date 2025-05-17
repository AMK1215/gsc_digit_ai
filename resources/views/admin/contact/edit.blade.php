@extends('admin_layouts.app')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.contact.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Edit Contact</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.contact.update', $contact->id ) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="custom-form-group">
                  <label for="title">Link</label>
                  <input type="text" class="form-control" id="" name="link" value="{{ $contact->link}}">
                </div>
                <div class="custom-form-group">
                  <label for="title">Choose Contact Type</label>
                  <select name="contact_type_id" id="" class="form-select form-control">
                    <option value="">Select Contact Type</option>
                    @foreach ($contact_types as $contact_type)
                      <option value="{{ $contact_type->id }}" {{ $contact->contact_type_id == $contact_type->id ? 'selected' : ''}}>{{ $contact_type->name }}</option>
                    @endforeach
                  </select>
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
  </div>
</div>

@endsection

