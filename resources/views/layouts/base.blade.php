<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Favicons -->
    <title>{{ env('APP_NAME') }} | @yield('PAGE_NAME')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/acclogo.png') }}">
    <meta name="msapplication-config" content="{{ asset('assets/img/favicons/browserconfig.xml') }}">
    <meta name="theme-color" content="#563d7c">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">

    <!-- Apex Charts -->
    <link type="text/css" href="{{ asset('vendor/apexcharts/apexcharts.css') }}" rel="stylesheet">

    <!-- Bootstrap (via CDN) -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Datepicker (via CDN) -->
    <link rel="stylesheet" href="{{ asset('css/datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datepicker-bs4.min.css') }}">

    <!-- Fontawesome -->
    <link type="text/css" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Sweet Alert -->
    <link type="text/css" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Notyf -->
    <link type="text/css" href="{{ asset('vendor/notyf/notyf.min.css') }}" rel="stylesheet">
    <!-- data tabe CSS
  ============================================ -->
    <link rel="stylesheet" href="{{ asset('css/data-table/bootstrap-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data-table/bootstrap-table-filter-control.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data-table/bootstrap-table-fixed-columns.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data-table/bootstrap-table-page-jump-to.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data-table/bootstrap-table-reorder-rows.css') }}">
    <link rel="stylesheet" href="{{ asset('css/data-table/bootstrap-table-sticky-header.css') }}">
    <!-- touchspin CSS
  ============================================ -->
    <link rel="stylesheet" href="{{ asset('css/touchspin/jquery.bootstrap-touchspin.min.css') }}">
    <!-- select2 CSS
  ============================================ -->
    <link rel="stylesheet" href="{{ asset('css/select2/select2.min.css') }}">
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/air-datepicker@3.2.1/air-datepicker.css">

    <!-- Volt CSS -->
    <link type="text/css" href="{{ asset('css/volt.css') }}" rel="stylesheet">
    @yield('custom-css')
</head>

<body>
    @php
        $currentRoutePattern = request()->path(); // Get the current URL path
        $currentRoute = request()->route()->getName();

    @endphp

    @if (Str::startsWith($currentRoutePattern, 'admin/') ||
            Str::startsWith($currentRoutePattern, 'president/') ||
            Str::startsWith($currentRoutePattern, 'department/') ||
            Str::startsWith($currentRoutePattern, 'custodian/') ||
            Str::startsWith($currentRoutePattern, 'employee/'))
        {{-- Nav --}}
        @include('layouts.nav')

        {{-- SideNav --}}
        @include('layouts.sidenav')

        <main class="content">
            {{-- TopBar --}}
            @include('layouts.topbar')

            <div class="py-4">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item">
                            <a href="#">
                                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">{{ ucwords(auth()->user()->role) }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('PAGE_NAME')</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between w-100 flex-wrap">
                    <div class="mb-3 mb-lg-0">
                        <h1 class="h4">@yield('PAGE_NAME')</h1>
                    </div>
                </div>
            </div>
            @yield('content')

            {{-- Footer --}}
            {{-- @include('layouts.footer') --}}
            <!-- Update Profile Modal -->
            <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form id="updateProfileForm" class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Update Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="{{ auth()->user()->first_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Middle Name (Optional)</label>
                                <input type="text" name="middle_name" class="form-control"
                                    value="{{ auth()->user()->middle_name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                    value="{{ auth()->user()->last_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Extension Name (Optional)</label>
                                <input type="text" name="extension_name" class="form-control"
                                    value="{{ auth()->user()->extension_name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_no" class="form-control"
                                    value="{{ auth()->user()->contact_no }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ auth()->user()->email }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update Profile</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="updatePasswordModal" tabindex="-1"
                aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form id="updatePasswordForm" class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">Update Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update Password</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    @elseif (Str::startsWith($currentRoutePattern, '/viewLogin'))
        {{-- Just show the content without nav and sidenav --}}
        @yield('content')
    @else
        {{-- Any other route (e.g., errors like 404 or 500) --}}
        @yield('content')
    @endif

    <!-- Core -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/js/on-screen.umd.min.js') }}"></script>

    <!-- Slider -->
    <script src="{{ asset('assets/js/nouislider.min.js') }}"></script>

    <!-- Smooth scroll -->
    <script src="{{ asset('assets/js/smooth-scroll.polyfills.min.js') }}"></script>

    <!-- Apex Charts -->
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Charts -->
    <script src="{{ asset('assets/js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartist-plugin-tooltip.min.js') }}"></script>

    <!-- Datepicker -->
    <script src="{{ asset('js/datepicker.min.js') }}"></script>

    <!-- Sweet Alerts 2 -->
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

    <!-- Moment JS -->
    <script src="{{ asset('js/moment.min.js') }}"></script>

    <!-- Notyf -->
    <script src="{{ asset('vendor/notyf/notyf.min.js') }}"></script>

    <!-- Simplebar -->
    <script src="{{ asset('assets/js/simplebar.min.js') }}"></script>

    <!-- Github buttons -->
    <script async defer src="{{ asset('js/buttons.js') }}"></script>
    <!-- data table JS
  ============================================ -->
    <script src="{{ asset('js/data-table/bootstrap-table.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-auto-refresh.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-copy-rows.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-defer-url.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-filter-control.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-fixed-columns.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-mobile.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-multiple-sort.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-page-jump-to.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-print.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-reorder-rows.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-sticky-header.js') }}"></script>
    <script src="{{ asset('js/data-table/bootstrap-table-toolbar.js') }}"></script>
    <script src="{{ asset('js/data-table/utils.js') }}"></script>
    <!-- input-mask JS
  ============================================ -->
    <script src="{{ asset('js/input-mask/jasny-bootstrap.min.js') }}"></script>
    <!-- touchspin JS
  ============================================ -->
    <script src="{{ asset('js/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <!-- select2 JS
  ============================================ -->
    <script src="{{ asset('js/select2/select2.full.min.js') }}"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('vendor/fullcalendar/main.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.2.1/air-datepicker.js"></script>

    <!-- Volt JS -->
    <script src="{{ asset('assets/js/volt.js') }}"></script>

    <script type="text/javascript">
        function showErrorMessage(message) {
            const notyf = new Notyf({
                duration: 5000, // Duration of notifications in milliseconds
                position: {
                    x: 'right',
                    y: 'top',
                },
                types: [{
                    type: 'error',
                    background: '#FA5252',
                    icon: {
                        className: 'fas fa-times',
                        tagName: 'span',
                        color: '#fff'
                    },
                    dismissible: false
                }]
            });
            notyf.open({
                type: 'error',
                message: message
            });
        }

        function showSuccessMessage(message) {
            const notyf = new Notyf({
                duration: 5000, // Duration of notifications in milliseconds
                position: {
                    x: 'right',
                    y: 'top',
                },
                types: [{
                    type: 'success',
                    background: '#198754',
                    icon: {
                        className: 'fas fa-check',
                        tagName: 'span',
                        color: '#fff'
                    },
                    dismissible: false
                }]
            });
            notyf.open({
                type: 'success',
                message: message
            });
        }

        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("select").select2({
                width: '100%'
            });

            var $table = $('#table');
            $table.bootstrapTable('destroy').bootstrapTable({
                buttonsAlign: 'right',
                toolbarAlign: 'right',
                searchAlign: 'left',
                classes: 'table table-bordered table-hover table-striped',
            });

            var $table1 = $('#table1');
            $table1.bootstrapTable('destroy').bootstrapTable({
                buttonsAlign: 'right',
                toolbarAlign: 'right',
                searchAlign: 'left',
                classes: 'table table-bordered table-hover table-striped',
            });

            $("#item_quantity").TouchSpin({
                min: 1,
                max: 1000,
                verticalbuttons: true,
                buttondown_class: 'btn btn-white',
                buttonup_class: 'btn btn-white'
            });

            $('#updateProfileForm').submit(function(event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    method: 'PUT',
                    url: '/users/updateProfile',
                    data: formData,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.valid) {
                            showSuccessMessage(response.msg);
                            $('#updateProfileModal').modal('hide');
                        }
                    },
                    error: function(jqXHR) {
                        let errorMsg = "An unexpected error occurred. Please try again.";

                        if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            errorMsg = `${jqXHR.responseJSON.msg}\n`;
                            for (const [field, messages] of Object.entries(jqXHR.responseJSON
                                    .errors)) {
                                errorMsg += `- ${messages.join(', ')}\n`;
                            }
                        }

                        showErrorMessage(errorMsg);
                    }
                });
            });

            $('#updatePasswordForm').submit(function(event) {
                event.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    method: 'PUT',
                    url: '/users/updatePassword',
                    data: formData,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.valid) {
                            showSuccessMessage(response.msg);
                            $('#updatePasswordModal').modal('hide');
                            $('#updatePasswordForm')[0].reset();
                        }
                    },
                    error: function(jqXHR) {
                        let errorMsg = "An unexpected error occurred. Please try again.";

                        if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            errorMsg = `${jqXHR.responseJSON.msg}\n`;
                            for (const [field, messages] of Object.entries(jqXHR.responseJSON
                                    .errors)) {
                                errorMsg += `- ${messages.join(', ')}\n`;
                            }
                        }

                        showErrorMessage(errorMsg);
                    }
                });
            });

        });
    </script>

    @yield('script')
</body>

</html>
