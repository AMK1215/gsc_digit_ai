@extends('admin_layouts.app')
@section('content')
<div class="row justify-content-center">
  <div class="col-10">
    <div class="container mt-0">
      <div class="d-flex justify-content-between">
        <h4>Game's Detail</h4>
        <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.games.index') }}">
          <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
        </a>
      </div>
      <div class="card p-4">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
              <tr>
                <td>ID</td>
                <td>{!! $game->id !!}</td>
              </tr>
              <tr>
                <td class="text-right">Image</td>
                <td>
                  <img src="{{ $game->img_url }}" class="w-25 img-thumbnail" alt="">
                </td>
              </tr>
              <tr>
                <td class="text-right">Name</td>
                <td>
                  {{ $game->name }}
                </td>
              </tr>
              <tr>
                <td class="text-right">Link</td>
                <td>
                    <a href="{{ $game->link }}">{{ $game->link }}</a>
                </td>
              </tr>
              <tr>
                <td>Created Date</td>
                <td>{!! $game->created_at->format('F j, Y') !!}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

