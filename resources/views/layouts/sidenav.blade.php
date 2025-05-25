<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
    <div class="sidebar-inner px-2 pt-3">
        <div
            class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
            <div class="d-flex align-items-center">
                <div class="avatar-lg me-4">
                    <img src="{{ asset('assets/img/team/profile-picture-3.jpg') }}"
                        class="card-img-top rounded-circle border-white" alt="User Avatar">
                </div>
                <div class="d-block">
                    <h2 class="h5 mb-3">
                        {{ auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'User Name' }}
                    </h2>
                    <a href="{{ route('logout') }}" class="btn btn-secondary btn-sm d-inline-flex align-items-center">
                        <i class="fas fa-sign-out-alt dropdown-icon text-gray-400 me-2"></i>
                        Sign Out
                    </a>
                </div>
            </div>
            <div class="collapse-close d-md-none">
                <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
                    aria-controls="sidebarMenu" aria-expanded="true" aria-label="Toggle navigation">
                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>
        <ul class="nav flex-column pt-3 pt-md-0">
            @if (auth()->user()->role === 'Custodian')
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon me-3">
                            <img src="/assets/img/brand/light.svg" height="20" width="20" alt="Volt Logo">
                        </span>
                        <span class="mt-1 ms-1 sidebar-text">
                            {{ auth()->user()->role }}
                        </span>
                    </a>
                </li>

                <li class="nav-item {{ Route::currentRouteName() == 'custodianDashboard' ? 'active' : '' }}">
                    <a href="{{ route('custodianDashboard') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-tachometer-alt icon-xs me-2"></i>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <!-- Transactions Dropdown -->
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#transactionsMenu"
                        aria-expanded="false" aria-controls="transactionsMenu">
                        <span class="sidebar-icon"><i class="fas fa-exchange-alt icon-xs me-2"></i></span>
                        <span class="sidebar-text">Transactions</span>
                    </a>
                    <div class="collapse" id="transactionsMenu">
                        <ul class="nav flex-column ms-4">
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'custodianItemRequest' ? 'active' : '' }}">
                                <a href="{{ route('custodianItemRequest') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-box-open icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Requested Stocks</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Management Dropdown -->
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#managementMenu" aria-expanded="false"
                        aria-controls="managementMenu">
                        <span class="sidebar-icon"><i class="fas fa-tasks icon-xs me-2"></i></span>
                        <span class="sidebar-text">Management</span>
                    </a>
                    <div class="collapse" id="managementMenu">
                        <ul class="nav flex-column ms-4">
                            <li class="nav-item {{ Route::currentRouteName() == 'custodianUnits' ? 'active' : '' }}">
                                <a href="{{ route('custodianUnits') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-archive icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Units</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Route::currentRouteName() == 'custodianItems' ? 'active' : '' }}">
                                <a href="{{ route('custodianItems') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-archive icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Stocks</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Reports Dropdown -->
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#reportsMenu" aria-expanded="false"
                        aria-controls="reportsMenu">
                        <span class="sidebar-icon"><i class="fas fa-chart-line icon-xs me-2"></i></span>
                        <span class="sidebar-text">Reports</span>
                    </a>
                    <div class="collapse" id="reportsMenu">
                        <ul class="nav flex-column ms-4">
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'custodianStockEquipmentReports' ? 'active' : '' }}">
                                <a href="{{ route('custodianStockEquipmentReports') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-boxes icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Stock</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'custodianItemRequestReports' ? 'active' : '' }}">
                                <a href="{{ route('custodianItemRequestReports') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-file-alt icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Stock Request</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            @if (auth()->user()->role === 'President')
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon me-3">
                            <img src="/assets/img/brand/light.svg" height="20" width="20" alt="Volt Logo">
                        </span>
                        <span class="mt-1 ms-1 sidebar-text">
                            {{ auth()->user()->role }}
                        </span>
                    </a>
                </li>

                <li class="nav-item {{ Route::currentRouteName() == 'presidentDashboard' ? 'active' : '' }}">
                    <a href="{{ route('presidentDashboard') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-tachometer-alt icon-xs me-2"></i>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <!-- Transactions Dropdown -->
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#transactionsMenu"
                        aria-expanded="false" aria-controls="transactionsMenu">
                        <span class="sidebar-icon"><i class="fas fa-exchange-alt icon-xs me-2"></i></span>
                        <span class="sidebar-text">Transactions</span>
                    </a>
                    <div class="collapse" id="transactionsMenu">
                        <ul class="nav flex-column ms-4">
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'presidentItemRequest' ? 'active' : '' }}">
                                <a href="{{ route('presidentItemRequest') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-box-open icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Requested Stocks</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'presidentFacilityReservation' ? 'active' : '' }}">
                                <a href="{{ route('presidentFacilityReservation') }}" class="nav-link">
                                    <span class="sidebar-icon">
                                        <i class="fas fa-building icon-xs me-2"></i> <!-- Icon for Facility Reservation -->
                                    </span>
                                    <span class="sidebar-text">Facility Reservation</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Management Dropdown -->
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#managementMenu"
                        aria-expanded="false" aria-controls="managementMenu">
                        <span class="sidebar-icon"><i class="fas fa-tasks icon-xs me-2"></i></span>
                        <span class="sidebar-text">Management</span>
                    </a>
                    <div class="collapse" id="managementMenu">
                        <ul class="nav flex-column ms-4">
                            <li class="nav-item {{ Route::currentRouteName() == 'presidentItems' ? 'active' : '' }}">
                                <a href="{{ route('presidentItems') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-archive icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Stocks</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Reports Dropdown -->
                <li class="nav-item">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#reportsMenu"
                        aria-expanded="false" aria-controls="reportsMenu">
                        <span class="sidebar-icon"><i class="fas fa-chart-line icon-xs me-2"></i></span>
                        <span class="sidebar-text">Reports</span>
                    </a>
                    <div class="collapse" id="reportsMenu">
                        <ul class="nav flex-column ms-4">
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'presidentStockEquipmentReports' ? 'active' : '' }}">
                                <a href="{{ route('presidentStockEquipmentReports') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-boxes icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Stock</span>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'presidentItemRequestReports' ? 'active' : '' }}">
                                <a href="{{ route('presidentItemRequestReports') }}" class="nav-link">
                                    <span class="sidebar-icon"><i class="fas fa-file-alt icon-xs me-2"></i></span>
                                    <span class="sidebar-text">Stock Request</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            @if (auth()->user()->role === 'Facilities In-charge')
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon me-3">
                            <img src="/assets/img/brand/light.svg" height="20" width="20" alt="Volt Logo">
                        </span>
                        <span class="mt-1 ms-1 sidebar-text">
                            {{ auth()->user()->role }}
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'departmentDashboard' ? 'active' : '' }}">
                    <a href="{{ route('departmentDashboard') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-tachometer-alt icon-xs me-2"></i>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'departmentFacilities' ? 'active' : '' }}">
                    <a href="{{ route('departmentFacilities') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-cogs icon-xs me-2"></i> <!-- More appropriate icon for Facilities -->
                        </span>
                        <span class="sidebar-text">Facilities</span>
                    </a>
                </li>
                <li
                    class="nav-item {{ Route::currentRouteName() == 'departmentFacilityReservation' ? 'active' : '' }}">
                    <a href="{{ route('departmentFacilityReservation') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-building icon-xs me-2"></i> <!-- Icon for Facility Reservation -->
                        </span>
                        <span class="sidebar-text">Facility Reservation</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'departmentReports' ? 'active' : '' }}">
                    <a href="{{ route('departmentReports') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-chart-line icon-xs me-2"></i> <!-- Using a new icon for Reports -->
                        </span>
                        <span class="sidebar-text">Reports</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role === 'Employee')
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon me-3">
                            <img src="/assets/img/brand/light.svg" height="20" width="20" alt="Volt Logo">
                        </span>
                        <span class="mt-1 ms-1 sidebar-text">
                            {{ auth()->user()->role }}
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'employeeDashboard' ? 'active' : '' }}">
                    <a href="{{ route('employeeDashboard') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-tachometer-alt icon-xs me-2"></i>
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'employeeFacilities' ? 'active' : '' }}">
                    <a href="{{ route('employeeFacilities') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-building icon-xs me-2"></i>
                        </span>
                        <span class="sidebar-text">Facilities</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'employeeItemRequest' ? 'active' : '' }}">
                    <a href="{{ route('employeeItemRequest') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-box icon-xs me-2"></i> <!-- Updated icon for Item Request -->
                        </span>
                        <span class="sidebar-text">Item Request</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role === 'System Admin')
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="sidebar-icon me-3">
                            <img src="/assets/img/brand/light.svg" height="20" width="20" alt="Volt Logo">
                        </span>
                        <span class="mt-1 ms-1 sidebar-text">
                            {{ auth()->user()->role }}
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'adminDashboard' ? 'active' : '' }}">
                    <a href="{{ route('adminDashboard') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-tachometer-alt icon-xs me-2"></i> <!-- Dashboard Icon -->
                        </span>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'adminUserAccount' ? 'active' : '' }}">
                    <a href="{{ route('adminUserAccount') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-user-circle icon-xs me-2"></i> <!-- User Account Icon -->
                        </span>
                        <span class="sidebar-text">User Account</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::currentRouteName() == 'adminUserManagement' ? 'active' : '' }}">
                    <a href="{{ route('adminUserManagement') }}" class="nav-link">
                        <span class="sidebar-icon">
                            <i class="fas fa-users-cog icon-xs me-2"></i> <!-- User Management Icon -->
                        </span>
                        <span class="sidebar-text">User Management</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</nav>
