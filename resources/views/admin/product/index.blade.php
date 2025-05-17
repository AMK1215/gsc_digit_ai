@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Provider Lists</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">
              {{-- <a href="{{ route('admin.products.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Provider</a> --}}
              {{-- <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" data-type="csv" type="button" name="button">Export</button> --}}
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="banners-search">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Code</th>
              <th>Type</th>
              <th>Image</th>
              <th>Status</th>
              <th>Order</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $product)
            <tr>
              <td class="text-sm font-weight-normal">{{ $loop->iteration }}</td>
              <td>
                {{ $product->name }}
              </td>
              <td>
                {{ $product->code }}
              </td>
              <td>
                {{ $product->game_type }}
              </td>
              <td>
                
                <img src="{{ $product->image }}" width="50px" class="rounded" alt="">
              </td>
              <td>
                {{ $product->status == 1 ? 'Active' : 'Inactive'}}
              </td>
              <td>
                {{ $product->order }}
              </td>
              <td>
                {{-- <a href="{{ route('admin.products.edit', $product->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Product"><i class="material-icons-round text-secondary position-relative text-lg">mode_edit</i></a> --}}
                {{-- <form class="d-inline" action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="transparent-btn" data-bs-toggle="tooltip" data-bs-original-title="Delete Product">
                    <i class="material-icons text-secondary position-relative text-lg">delete</i>
                  </button>
                </form> --}}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  if (document.getElementById('banners-search')) {
    const dataTableSearch = new simpleDatatables.DataTable("#banners-search", {
      searchable: true,
      fixedHeight: false,
      perPage: 7
    });

    document.querySelectorAll(".export").forEach(function(el) {
      el.addEventListener("click", function(e) {
        var type = el.dataset.type;

        var data = {
          type: type,
          filename: "material-" + type,
        };

        if (type === "csv") {
          data.columnDelimiter = "|";
        }

        dataTableSearch.export(data);
      });
    });
  };
</script>
<script>
  $(document).ready(function() {
    $('.transparent-btn').on('click', function(e) {
      e.preventDefault();
      let form = $(this).closest('form');
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
</script>
@endsection
