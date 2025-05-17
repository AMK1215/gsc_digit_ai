@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-10">
    <div class="container mt-0">
      <div class="d-flex justify-content-between">
        <h4>Banner's Detail</h4>
        <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.banners.index') }}">
          <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
        </a>
      </div>
      <div class="card">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
              <tr>
                <td>ID</td>
                <td>{!! $banner->id !!}</td>
              </tr>
              <tr>
                <td class="text-right">Image</td>
                <td>
                  <img src="{{ $banner->img_url }}" class="w-25 img-thumbnail" alt="">
                </td>
              </tr>
              <tr>
                <td>Create Date</td>
                <td>{!! $banner->created_at->format('F j, Y') !!}</td>
              </tr>
              <tr>
                <td>Update Date</td>
                <td>{!! $banner->updated_at->format('F j, Y') !!}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
