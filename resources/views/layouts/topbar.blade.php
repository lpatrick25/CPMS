<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between w-100" id="navbarSupportedContent">
            <div class="d-flex align-items-center">

            </div>
            <!-- Navbar links -->
            <ul class="navbar-nav align-items-center">
                @if (in_array(auth()->user()->role, ['President', 'Custodian', 'Employee']))
                    <li class="nav-item dropdown">
                        <a class="nav-link text-dark notification-bell unread dropdown-toggle"
                            data-unread-notifications="true" href="#" role="button" data-bs-toggle="dropdown"
                            data-bs-display="static" aria-expanded="false">
                            <svg class="icon icon-sm text-gray-900" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
                                </path>
                            </svg>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-center mt-2 py-0">
                            <div class="list-group list-group-flush">
                                <a href="#"
                                    class="text-center text-primary fw-bold border-bottom border-light py-3">Notifications</a>
                                @foreach ($notifications as $notification)
                                    <a href="#"
                                        class="list-group-item list-group-item-action border-bottom {{ $notification->is_read ? '' : 'bg-light' }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img alt="Image placeholder"
                                                    src="/assets/img/team/profile-picture-1.jpg"
                                                    class="avatar-md rounded">
                                            </div>
                                            <div class="col ps-0 ms-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="h6 mb-0 text-small">
                                                            {{ $notification->sender->first_name ?? 'System' }}
                                                            {{ $notification->sender->middle_name[0] ?? 'S' }}
                                                            {{ $notification->sender->last_name ?? 'System' }}</h4>
                                                    </div>
                                                    <div class="text-end">
                                                        <small
                                                            class="text-danger">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <p class="font-small mt-1 mb-0">{{ $notification->message }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </li>
                @endif
                <li class="nav-item dropdown ms-lg-3">
                    <a class="nav-link dropdown-toggle pt-1 px-0" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="media d-flex align-items-center">
                            <img class="avatar rounded-circle" alt="Image placeholder"
                                src="/assets/img/team/profile-picture-1.jpg">
                            <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                                <span
                                    class="mb-0 font-small fw-bold text-gray-900">{{ auth()->user()->first_name ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'User Name' }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                        <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal"
                            data-bs-target="#updateProfileModal">
                            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            My Profile
                        </a>

                        <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal"
                            data-bs-target="#updatePasswordModal">
                            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Update Password
                        </a>

                        <div role="separator" class="dropdown-divider my-1"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center">
                            <i class="fas fa-sign-out-alt dropdown-icon text-gray-400 me-2"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
