<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\User;

class NavigationController extends Controller
{

    public function viewLogin()
    {
        if (auth()->user()) {
            // Get the user after authentication
            $user = auth()->user();

            // Redirect based on role after login
            switch ($user->role) {
                case 'Admin':
                    return redirect()->route('adminDashboard');
                case 'President':
                    return redirect()->route('presidentDashboard');
                case 'Department Head':
                    return redirect()->route('departmentDashboard');
                case 'Property Custodian':
                    return redirect()->route('custodianDashboard');
                case 'Employee':
                    return redirect()->route('employeeDashboard');
                default:
                    // Default fallback if the role doesn't match
                    return view('login');
            }
        }
        return view('login');
    }

    // Custodian Navigation

    public function custodianDashboard()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('custodian.dashboard', compact('notifications'));
    }

    public function custodianUnits()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('custodian.units', compact('notifications'));
    }

    public function custodianItems()
    {
        $units = Unit::all();
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('custodian.items', compact('notifications', 'units'));
    }

    public function custodianItemRequest()
    {
        $items = Item::all();
        $employees = User::where('role', '=', 'Employee')->get();
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($employees as $employee) {
            $fullname = $this->formatFullName(
                $employee->first_name ?? '',
                $employee->middle_name ?? '',
                $employee->last_name ?? '',
                $employee->extension_name ?? ''
            );

            // Assign the computed full name to the 'employee_name' attribute
            $employee->employee_name = $fullname;
        }

        return view('custodian.item_request', compact('employees', 'items', 'notifications'));
    }

    /**
     * Format the full name with proper handling of the middle name.
     *
     * @param string $firstName
     * @param string|null $middleName
     * @param string $lastName
     * @param string|null $extensionName
     * @return string
     */
    private function formatFullName($firstName, $middleName, $lastName, $extensionName)
    {
        $middleInitial = $middleName ? strtoupper(substr($middleName, 0, 1)) . '.' : '';
        $fullName = trim("{$firstName} {$middleInitial} {$lastName} {$extensionName}");
        return $fullName;
    }

    public function custodianItemRequestReports()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $employees = User::where('role', 'Employee')->get();
        return view('custodian.item_request_reports', compact('employees', 'notifications'));
    }

    public function custodianStockEquipmentReports()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('custodian.stock_equipment_report', compact('notifications'));
    }

    // Employee Navigation

    public function employeeDashboard()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('employee.dashboard', compact('notifications'));
    }

    public function employeeFacilities()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('employee.facilities', compact('notifications'));
    }

    public function facilitiesReservation($facility_id)
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('employee.facility_reservation', compact('facility_id', 'notifications'));
    }

    public function employeeItemRequest()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $items = Item::all();
        return view('employee.item_request', compact('items', 'notifications'));
    }

    // Admin Navigation

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function adminUserAccount()
    {
        return view('admin.user_account');
    }

    public function adminUserManagement()
    {
        return view('admin.user_management');
    }

    // President

    public function presidentDashboard()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('president.dashboard', compact('notifications'));
    }

    public function presidentItems()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('president.items', compact('notifications'));
    }

    public function presidentItemRequest()
    {
        $items = Item::all();
        $employees = User::where('role', '=', 'Employee')->get();
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($employees as $employee) {
            $fullname = $this->formatFullName(
                $employee->first_name ?? '',
                $employee->middle_name ?? '',
                $employee->last_name ?? '',
                $employee->extension_name ?? ''
            );

            // Assign the computed full name to the 'employee_name' attribute
            $employee->employee_name = $fullname;
        }

        return view('president.item_request', compact('employees', 'items', 'notifications'));
    }

    public function presidentFacilityReservation()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('president.facility_reservation', compact('notifications'));
    }

    public function presidentItemRequestReports()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $employees = User::where('role', 'Employee')->get();
        return view('president.item_request_reports', compact('employees', 'notifications'));
    }

    public function presidentStockEquipmentReports()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('president.stock_equipment_report', compact('notifications'));
    }

    // Department Dashboard

    public function departmentDashboard()
    {
        return view('department.dashboard');
    }

    public function departmentFacilities()
    {
        return view('department.facilities');
    }

    public function departmentFacilityReservation()
    {
        return view('department.facility_reservation');
    }

    public function departmentReports()
    {
        $employees = User::where('role', 'Employee')->get();
        return view('department.reports', compact('employees'));
    }
}
