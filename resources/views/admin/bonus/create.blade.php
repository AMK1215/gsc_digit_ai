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
                            <h5 class="mb-0">Add Bonus</h5>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.bonus.index') }}">
                                    <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-form-group">
                        <label for="title" style="text-align: justify;">Player ID</label>
                        <input type="text"  placeholder="search player id" name="user_name" id="user_name" value="{{old('user_name')}}">
                    </div> 
                    <form role="form" method="POST" class="text-start" action="{{ route('admin.bonus.store') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{old('id')}}">
                        <div class="custom-form-group">
                            <label for="title">BonusTypes</label>
                            <select name="type_id" class="form-control form-select" id="">
                                <option value="">Select Bonustype</option>
                                @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('type_id')
                            <span class="text-danger">*{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="custom-form-group">
                            <label for="title">UserName<span class="text-danger">*</span></label>
                            <input type="text" name="user_name" class="form-control" value="{{old('user_name')}}">
                            @error('user_name')
                            <span class="text-danger d-block">*{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="custom-form-group">
                            <label for="title">PlayerName<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{old('name')}}">
                            @error('name')
                            <span class="text-danger d-block">*{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="custom-form-group">
                            <label for="title">Amount<span class="text-danger">*</span></label>
                            <input type="text" name="amount" class="form-control" value="{{old('amount')}}" placeholder="0.00">
                            @error('amount')
                            <span class="text-danger d-block">*{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="custom-form-group">
                            <label for="title">Remark</label>
                            <input type="text" name="" id="" class="form-control">
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
    $(document).ready(function() {
        $('#user_name').on('keyup', function() {
            let playerId = $(this).val();

            if (playerId.trim() !== '') {
                $.ajax({
                    url: '/admin/bonusPlayer', 
                    type: 'GET',
                    data: { user_name: playerId },
                    success: function(response) {
                        if (response.success && response.data) {
                            let player = response.data;
                            $('input[name="user_name"]').val(player.user_name);
                            $('input[name="name"]').val(player.name);
                            $('input[name="phone"]').val(player.phone);
                            $('input[name="id"]').val(player.id);
                            $('span.badge').text(player.balance.toFixed(2)); // Display formatted balance
                        } else {
                            clearPlayerData();
                            alert('Player not found');
                        }
                    },
                    error: function(xhr, status, error) {
                       
                        clearPlayerData();
                    }
                });
            } else {
                clearPlayerData();
            }
        });

        // Function to clear player-related form fields
        function clearPlayerData() {
            $('input[name="user_name"]').val('');
            $('input[name="name"]').val('');
            $('input[name="phone"]').val('');
            $('input[name="id"]').val('');
            $('span.badge').text('');
        }
    });
</script>


@endsection