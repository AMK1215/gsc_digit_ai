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
                                <h5 class="mb-0">Change Password</h5>

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
                        <form role="form" method="POST" class="text-start"
                            action="{{ route('admin.master.makeChangePassword', $master->id) }}">
                            @csrf
                            <div class="custom-form-group">
                                <label for="title">New Password <span class="text-danger">*</span></label>
                                <input type="text" name="password" class="form-control">
                                @error('password')
                                    <span class="text-danger d-block">*{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="custom-form-group">
                                <label for="title">Confirm Password <span class="text-danger">*</span></label>
                                <input type="text" name="password_confirmation" class="form-control">
                            </div>

                            <div class="custom-form-group">
                                <button type="submit" class="btn btn-primary" type="button">Confirm</button>
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
