@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-10">
    <div class="container mt-0">
      <div class="d-flex justify-content-between">
        <h4>Banner's Detail</h4>
        <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.adsbanners.index') }}">
          <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
        </a>
      </div>
      <div class="card">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
               <tr>
                            <th>ID</th>
                            <td>{!! $adsbanner->id !!}</td>
                        </tr>
                        <tr>
                            <th>Image</th>
                            <td>
                                @if ($adsbanner->img_url)
                                    <img src="{{ $adsbanner->img_url }}" width="150px" class="img-thumbnail" alt="">
                                @else
                                    No Image
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Create Date</th>
                            <td>{!! $adsbanner->created_at ? $adsbanner->created_at->format('F j, Y') : 'N/A' !!}</td>
                        </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
