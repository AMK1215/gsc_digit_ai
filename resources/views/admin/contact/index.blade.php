@extends('admin_layouts.app')
@section('styles')
<style>
  .transparent-btn {
    background: none;
    border: none;
    padding: 0;
    outline: none;
    cursor: pointer;
    box-shadow: none;
    appearance: none;
    /* For some browsers */
  }
</style>
@endsection
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Contact Lists</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">
              <a href="{{ route('admin.contact.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Contact</a>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="contact-search">
          <thead class="thead-light">
            <tr>
              <th>Link</th>
              <th>Icon</th>
              <th>Contact Type</th>
              <th>Agent</th>
            </tr>
          </thead>
          <tbody>
            @foreach($contacts as $key => $contact)
            <tr>
              <td class="text-sm font-weight-normal">{{ $contact->link }}</td>
              <td class="text-sm font-weight-normal">
                <img src="{{ $contact->contact_type->img_url }}" width="30px" alt="">
              </td>
              <td class="text-sm font-weight-normal">{{ $contact->contact_type->name }}</td>
              <td class="text-sm font-weight-normal">{{ $contact->agent->name }}</td>
              <td>
                <a href="{{ route('admin.contact.edit', $contact->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Contact"><i class="material-icons-round text-secondary position-relative text-lg">mode_edit</i></a>
                <form class="d-inline" action="{{ route('admin.contact.destroy', $contact->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="transparent-btn" data-bs-toggle="tooltip" data-bs-original-title="Delete Contact">
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
  if (document.getElementById('contact-search')) {
    const dataTableSearch = new simpleDatatables.DataTable("#contact-search", {
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
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
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
@if(session()->has('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: '{{ session('
    success ') }}',
    showConfirmButton: false,
    timer: 1500
  })
</script>
@endif


@endsection
