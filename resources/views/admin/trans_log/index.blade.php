@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
 <div class="col-12">
  <div class="card">
  <div class="card-header pb-0">
        <div class="card-body">
          <h5 class="mb-0">Transfer Logs</h5>
        </div>
      </div>
   <div class="table-responsive">
    <table class="table table-flush" id="users-search">
     <thead class="thead-light">

        <tr>
            <th>Date</th>
            <th>To User</th>
            <th>Amount</th>
            <th>Type</th>
            <th>CreatedBy</th>
            <th>Note</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transferLogs as $log)
        
            <tr>
                <td>
                  {{ $log->created_at }}
                </td>
                <td>{{ $log->targetUser->name }}</td>
                <td>
                  <div class="d-flex align-items-center text-{{$log->type =='withdraw' ? 'success' : 'danger'}} text-gradient text-sm font-weight-bold ms-auto"> {{ abs($log->amountFloat)}}</div>
                </td>
                <td>
                    @if($log->type == 'withdraw')
                        <p class="text-success font-weight-bold">Deposit</p>
                    @else
                        <p class="text-danger font-weight-bold">Withdraw</p>
                    @endif
                </td>
                <td>{{$log->user->name ?? ''}}</td>
                <td>{{$log->note}}</td>
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
if (document.getElementById('users-search')) {
 const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
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

@endsection
