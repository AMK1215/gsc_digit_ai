@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">BonusTypes</h5>

          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="banners-search">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            @foreach($data as $key => $type)
            <tr>
              <td class="text-sm font-weight-normal">{{ ++$key }}</td>
              <td class="text-sm font-weight-normal">{{ $type->name }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection