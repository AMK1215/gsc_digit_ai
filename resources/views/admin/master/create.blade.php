@extends('admin_layouts.app')
@section('content')
    <div class="container text-center mt-4">
        <div class="row">
            <div class="col-12 col-md-8 mx-auto">
                <div class="card">
                    <!-- Card header -->

                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">Create New Master</h5>

                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto">
                                    <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.master.index') }}">
                                        <span class="btn-inner--icon mt-1"><i
                                                class="material-icons">arrow_back</i>Back</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form role="form" method="POST" class="text-start" action="{{ route('admin.master.store') }}">
                            @csrf
                            <div class="custom-form-group">
                                <label for="title">Master ID <span class="text-danger">*</span></label>
                                <input type="text" name="user_name" class="form-control" value="{{ $user_name }}"
                                    readonly>
                                @error('name')
                                    <span class="text-danger d-block">*{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="custom-form-group">
                                <label for="title">Master Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    placeholder="Enter Name">
                                @error('player_name')
                                    <span class="text-danger d-block">*{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Password <span class="text-danger">*</span></label>
                                <input type="text" name="password" class="form-control" value="{{ old('password') }}"
                                    placeholder="Enter Password">
                                @error('password')
                                    <span class="text-danger d-block">*{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Phone No</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger d-block">*{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="custom-form-group">
                                <label>Max Balance : </label>
                                <span class="badge badge-sm bg-gradient-success">{{ auth()->user()->balanceFloat }}</span>
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Amount</label>
                                <input type="text" name="amount" class="form-control" value="{{ old('amount') }}"
                                    placeholder="0.00">
                                @error('amount')
                                    <span class="text-danger d-block">*{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <button class="btn btn-info" type="button" id="resetFormButton">Cancel</button>

                                <button type="submit" class="btn btn-primary" type="button">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var errorMessage = @json(session('error'));
        var successMessage = @json(session('success'));
        var url = 'https://moneyking77.online/login';
        var name = @json(session('username'));
        var pw = @json(session('password'));

        @if (session()->has('success'))
            Swal.fire({
                title: successMessage,
                icon: "success",
                showConfirmButton: false,
                showCloseButton: true,
                html: `
                    <table class="table table-bordered" style="background:#eee;">
                    <tbody>
                    <tr>
                        <td>username</td>
                        <td id="tusername"> ${name}</td>
                    </tr>
                    <tr>
                        <td>pw</td>
                        <td id="tpassword"> ${pw}</td>
                    </tr>
                    <tr>
                        <td>url</td>
                        <td id=""> ${url}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><a href="#" onclick="copy()" class="btn btn-sm btn-primary">copy</a></td>
                    </tr>
                    </tbody>
                    </table>
                `
            });
        @elseif (session()->has('error'))
            Swal.fire({
                icon: 'error',
                title: errorMessage,
                showConfirmButton: false,
                timer: 1500
            })
        @endif

        function copy() {
            var username = $('#tusername').text();
            var password = $('#tpassword').text();
            var copy = "url : " + url + "\nusername : " + username + "\npw : " + password;
            copyToClipboard(copy)
        }

        function copyToClipboard(v) {
            var $temp = $("<textarea>");
            $("body").append($temp);
            var html = v;
            $temp.val(html).select();
            document.execCommand("copy");
            $temp.remove();
        }
    </script>
@endsection
