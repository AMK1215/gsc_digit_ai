 <div class="sidenav-header">
     <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
         aria-hidden="true" id="iconSidenav"></i>
         <div class="navbar-brand m-0 d-flex align-items-center">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo-mk.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            </a>
            <div class="d-flex align-items-center ms-2">
                <span class="font-weight-bold text-white">MoneyKing</span>
                <a href="{{ route('admin.profile.index') }}" class="ms-2">
                    <span class="badge bg-success">
                        {{ Auth::user()->roles->pluck('title')->implode(', ') }}
                    </span>
                </a>
            </div>
        </div>
 </div>
