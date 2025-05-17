@extends('admin_layouts.app')
@section('content')

<div class="container-fluid my-3 py-3">
    <div class="row mb-5">

        <div class="col-lg-9 mt-lg-0 mt-4">

            <div class="card mt-4" id="password">
                <div class="card-header">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('admin.profile.updatePassword',$user->id) }}" method="POST">
                        @csrf
                        <div class="input-group input-group-outline my-4">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{auth()->user()->name}}">
                        </div>
                        <div class="input-group input-group-outline my-4">
                            <label class="form-label">New password</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        @error('password')
                        <span class="d-block text-danger">*{{ $message }}</span>
                        @enderror
                        <div class="input-group input-group-outline">
                            <label class="form-label">Confirm New password</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>

                        <button class="btn bg-gradient-dark btn-sm float-end mt-6 mb-0" type="submit">Update password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
    