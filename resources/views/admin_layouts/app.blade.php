@include('admin_layouts.head')
@yield('styles')

<body class="g-sidenav-show  bg-gray-200">
    <aside
        class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
        id="sidenav-main">
        @include('admin_layouts.sidebar_header')
        <hr class="horizontal light mt-0 mb-2">
        @include('admin_layouts.sidebar')
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        @include('admin_layouts.navbar')
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-lg-12 ">
                    @yield('content')
                </div>
            </div>
            @include('admin_layouts.footer')
        </div>
    </main>
    @include('admin_layouts.setting')

    <!--   Core JS Files   -->
    <script src="{{ asset('admin_app/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/flatpickr.js') }}"></script>
    <script src="{{asset('admin_app/assets/js/sweetalert2.all.min.js ') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>
    <script src="https://kit.fontawesome.com/b829c5162c.js" crossorigin="anonymous"></script>
    <script src="{{ asset('admin_app/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    <script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script src="{{ asset('admin_app/assets/js/material-dashboard.min.js?v=3.0.6') }}"></script>
    <script>
        $(document).ready(function() {
            var today = new Date();
            var startDate = new Date(today.setHours(0, 0, 0, 0));
            var endDate = new Date(today.setHours(23, 59, 59, 999));

            $('input[name="start_date"]').flatpickr({
                enableTime: true,
                dateFormat: "m/d/Y H:i",
            });
            $('input[name="end_date"]').flatpickr({
                enableTime: true,
                dateFormat: "m/d/Y H:i",
            });
            $('input[name="last_login_time"]').flatpickr({
                enableTime: true,
                dateFormat: "m/d/Y H:i",
            });
        });
    </script>
    <script>
        var errorMessage = @json(session('error'));
        var successMessage = @json(session('success'));
        @if(session() -> has('success'))
        Swal.fire({
            title: successMessage,
            icon: "success",
            showConfirmButton: false,
            showCloseButton: true,
        });
        @elseif(session() -> has('error'))
        Swal.fire({
            icon: 'error',
            title: errorMessage,
            showConfirmButton: false,
            timer: 1500
        })
        @endif
    </script>
    @yield('scripts')

</body>

</html>
