@extends('admin_layouts.app')
@section('content')

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header">
                <div class="d-lg-flex">
                    <div>
                        <h5 class="mb-0">DepositRequest Detail</h5>
                    </div>
                    <div class="ms-auto my-auto mt-lg-0 mt-4">
                        <div class="ms-auto my-auto">
                            <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.agent.deposit') }}">
                                <span class="btn-inner--icon mt-1"><i
                                        class="material-icons">arrow_back</i>Back</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{asset('assets/img/deposit/'. $deposit->image) }}" class="img-fluid rounded"
                            alt="">
                    </div>
                    <div class="col-md-6">
                        <div class="custom-form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $deposit->user->name }}"
                                readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control" name="amount" value="{{ number_format($deposit->amount, 2) }}"
                                readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">DateTime</label>
                            <input type="text" class="form-control" name="amount"
                                value="{{ $deposit->created_at->format('d-m-Y H:i:s') }}"
                                readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">Bank Account Name</label>
                            <input type="text" class="form-control" name="account_name"
                                value="{{ $deposit->bank->account_name }}" readonly>
                        </div>
                        <div class="custom-form-group"><label class="form-label">Bank Account No</label>
                            <input type="text" class="form-control" name="account_number"
                                value="{{ $deposit->bank->account_number }}" readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">Payment Method</label>
                            <input type="text" class="form-control" name=""
                                value="{{ $deposit->bank->paymentType->name }}" readonly>
                        </div>
                        <div class="d-lg-flex">
                            <form action="{{ route('admin.agent.depositStatusreject', $deposit->id) }}"
                                method="post">
                                @csrf
                                <input type="hidden" name="status" value="2">
                                @if($deposit->status == 0)
                                <button class="btn btn-danger" type="submit">
                                    Reject
                                </button>
                                @endif
                            </form>
                            <form action="{{ route('admin.agent.depositStatusUpdate', $deposit->id) }}"
                                method="post">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $deposit->amount }}">
                                <input type="hidden" name="status" value="1">
                                <input type="hidden" name="player" value="{{ $deposit->user_id }}">
                                @if($deposit->status == 0)
                                <button class="btn btn-success" type="submit" style="margin-left: 5px" id="submit">
                                    Approve
                                </button>
                                @endif
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
