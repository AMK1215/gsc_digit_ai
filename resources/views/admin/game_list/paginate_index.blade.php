@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header pb-0">
                <div class="d-lg-flex">
                    <div>
                        <h5 class="mb-0">Game List Dashboards
                           
                        </h5>
                    </div>
                    <div class="ms-auto my-auto mt-lg-0 mt-4">

                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-flush" id="users-search">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="bg-success text-white">Game Type</th>
                            <th class="bg-danger text-white">Product</th>
                            <th class="bg-info text-white">Game Name</th>
                            <th class="bg-warning text-white">Image</th>
                            <th class="bg-success text-white">Status</th>
                            <th class="bg-info text-white">Hot Status</th>
                            <th class="bg-warning text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#users-search').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.gameLists.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'game_type', name: 'gameType.name'},
            {data: 'product', name: 'product'},
            {data: 'game_name', name: 'game_name'},
            {data: 'image_url', name: 'image_url', render: function(data, type, full, meta) {
                return '<img src="' + data + '" width="100px">';
            }},
            {data: 'status', name: 'status'},
            {data: 'hot_status', name: 'hot_status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        language: {
            paginate: {
                next: '<i class="fas fa-angle-right"></i>', // or '→'
                previous: '<i class="fas fa-angle-left"></i>' // or '←'
            }
        },
        pageLength: 10, // Adjust this to your preference
    });
});
</script>


@endsection
