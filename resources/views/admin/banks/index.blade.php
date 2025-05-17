@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Bank Lists</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">
              <a href="{{ route('admin.banks.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Bank</a>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="banners-search">
          <thead class="thead-light">
            <tr>
              <th>No</th>
              <th>Bank</th>
              <th>Logo</th>
              <th>Account Name</th>
              <th>Account No</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($banks as $bank)
            <tr>
              <td class="text-sm font-weight-normal">{{ $loop->iteration }}</td>
              <td class="text-sm">{{$bank->paymentType->name}}</td>
              <td class="text-sm">
                <img src="{{$bank->paymentType->img_url}}" class="rounded" width="30px" alt="">
              </td>
              <td class="text-sm font-weight-normal">{{ $bank->account_name }}</td>
              <td class="text-sm font-weight-normal">{{ $bank->account_number }}</td>
              <td>
                    <div class="form-check form-switch">
                        <input 
                            type="checkbox" 
                            class="form-check-input toggle-status" 
                            data-id="{{ $bank->id }}" 
                            {{ $bank->status ? 'checked' : '' }}>
                    </div>
                </td>
              <td>
                <a href="{{ route('admin.banks.edit', $bank->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Bank"><i class="material-icons-round text-secondary position-relative text-lg">mode_edit</i></a>

                <form class="d-inline" action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="transparent-btn" data-bs-toggle="tooltip" data-bs-original-title="Delete Banner">
                    <i class="material-icons text-secondary position-relative text-lg">delete</i>
                  </button>
                </form>
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
$(document).ready(function () {
    $('.toggle-status').change(function () {
        let status = $(this).is(':checked') ? 1 : 0;
        let itemId = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.bank.status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: itemId,
                status: status,
            },
            success: function (response) {
                if (response.success) {
                    alert('Status updated successfully!');
                } else {
                    alert('Failed to update status.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again later.');
            }
        });
    });
});
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