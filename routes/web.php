<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FacilityReservationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Models\Item;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/viewLogin');

Route::get('/viewLogin', [NavigationController::class, 'viewLogin'])->name('viewLogin')->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');
// Route::put('/updatePassword/{userID}', [UserController::class, 'updatePassword'])->name('updatePassword');
Route::get('/forgot-password', [UserController::class, 'forgot-password'])->name('forgot-password');

Route::middleware(['auth', 'user'])->group(function () {

    Route::prefix('/admin')->group(function () {
        Route::get('/dashboard', [NavigationController::class, 'adminDashboard'])->name('adminDashboard');
        Route::get('/userAccount', [NavigationController::class, 'adminUserAccount'])->name('adminUserAccount');
        Route::get('/userManagement', [NavigationController::class, 'adminUserManagement'])->name('adminUserManagement');
    });

    Route::prefix('/president')->group(function () {
        Route::get('/dashboard', [NavigationController::class, 'presidentDashboard'])->name('presidentDashboard');
        Route::get('/items', [NavigationController::class, 'presidentItems'])->name('presidentItems');
        Route::get('/itemRequest', [NavigationController::class, 'presidentItemRequest'])->name('presidentItemRequest');
        Route::put('/updateStatus/{requestID}', [ItemRequestController::class, 'updateStatus'])->name('presidentUpdateStatus');
        Route::get('/equipments', [NavigationController::class, 'presidentEquipment'])->name('presidentEquipment');

        Route::get('/facilityReservation', [NavigationController::class, 'presidentFacilityReservation'])->name('presidentFacilityReservation');
        Route::put('/updateStatusReservation/{reservationID}', [FacilityReservationController::class, 'updateStatus'])->name('presidentFacilityUpdateStatus');

        Route::get('/equipmentRequest', [NavigationController::class, 'presidentEquipmentRequest'])->name('presidentEquipmentRequest');

        Route::get('/itemRequestReports', [NavigationController::class, 'presidentItemRequestReports'])->name('presidentItemRequestReports');
        Route::get('/equipmentRequestReports', [NavigationController::class, 'presidentEquipmentRequestReports'])->name('presidentEquipmentRequestReports');
        Route::get('/stockEquipmentReports', [NavigationController::class, 'presidentStockEquipmentReports'])->name('presidentStockEquipmentReports');
    });

    Route::prefix('/department')->group(function () {
        Route::get('/dashboard', [NavigationController::class, 'departmentDashboard'])->name('departmentDashboard');
        Route::get('/facilities', [NavigationController::class, 'departmentFacilities'])->name('departmentFacilities');
        Route::get('/facilityReservation', [NavigationController::class, 'departmentFacilityReservation'])->name('departmentFacilityReservation');
        Route::put('/updateStatus/{reservationID}', [FacilityReservationController::class, 'updateStatus'])->name('departmentUpdateStatus');
        Route::get('/reports', [NavigationController::class, 'departmentReports'])->name('departmentReports');
    });

    Route::prefix('/custodian')->group(function () {
        Route::get('/dashboard', [NavigationController::class, 'custodianDashboard'])->name('custodianDashboard');
        Route::get('/units', [NavigationController::class, 'custodianUnits'])->name('custodianUnits');
        Route::get('/items', [NavigationController::class, 'custodianItems'])->name('custodianItems');
        Route::get('/itemRequest', [NavigationController::class, 'custodianItemRequest'])->name('custodianItemRequest');
        Route::put('/updateStatus/{requestID}', [ItemRequestController::class, 'updateStatus'])->name('custodianUpdateStatus');
        Route::get('/equipments', [NavigationController::class, 'custodianEquipment'])->name('custodianEquipment');
        Route::get('/equipmentRequest', [NavigationController::class, 'custodianEquipmentRequest'])->name('custodianEquipmentRequest');
        Route::get('/itemRequestReports', [NavigationController::class, 'custodianItemRequestReports'])->name('custodianItemRequestReports');
        Route::get('/equipmentRequestReports', [NavigationController::class, 'custodianEquipmentRequestReports'])->name('custodianEquipmentRequestReports');
        Route::get('/stockEquipmentReports', [NavigationController::class, 'custodianStockEquipmentReports'])->name('custodianStockEquipmentReports');
    });

    Route::prefix('/equipment')->group(function () {
        Route::get('/dashboard', [NavigationController::class, 'equipmentDashboard'])->name('equipmentDashboard');
        Route::get('/equipment', [NavigationController::class, 'equipmentEquipments'])->name('equipmentEquipments');
        Route::get('/equipmentRequest', [NavigationController::class, 'equipmentEquipmentRequest'])->name('equipmentEquipmentRequest');
        Route::get('/reports', [NavigationController::class, 'equipmentReports'])->name('equipmentReports');
        Route::get('/equipmentReports', [NavigationController::class, 'equipmentEquipmentReports'])->name('equipmentEquipmentReports');
    });

    Route::prefix('/employee')->group(function () {
        Route::get('/dashboard', [NavigationController::class, 'employeeDashboard'])->name('employeeDashboard');
        Route::get('/facilities', [NavigationController::class, 'employeeFacilities'])->name('employeeFacilities');
        Route::get('/facilitiesReservation/{facilitiesID}', [NavigationController::class, 'facilitiesReservation'])->name('facilitiesReservation');
        Route::get('/itemRequest', [NavigationController::class, 'employeeItemRequest'])->name('employeeItemRequest');
        Route::get('/equipments', [NavigationController::class, 'employeeEquipments'])->name('employeeEquipments');
    });
});

// Report
Route::get('/facilityReservationReport', [ReportController::class, 'facilityReservationReport'])->name('facilityReservationReport');
Route::get('/equipmentBorrowingReport', [ReportController::class, 'equipmentBorrowingReport'])->name('equipmentBorrowingReport');
Route::get('/itemRequestReport', [ReportController::class, 'itemRequestReport'])->name('itemRequestReport');
Route::get('/getStockItemsReports', [ReportController::class, 'getStockItemsReports'])->name('getStockItemsReports');
Route::get('/getEquipmentReports', [ReportController::class, 'getEquipmentReports'])->name('getEquipmentReports');
Route::get('/download-stock-report', [ReportController::class, 'downloadStockItemsReport'])->name('downloadStockItemsReport');
Route::get('/download-equipment-report', [ReportController::class, 'downloadEquipmentReport'])->name('downloadEquipmentReport');

Route::get('/critical-items', function () {
    $criticalItems = Item::where('remaining_quantity', '<=', 10)->get();
    return response()->json($criticalItems);
});

Route::middleware('auth')->group(function () {
    Route::put('/users/updateProfile', [UserController::class, 'updateProfile'])->name('updateProfile');
    Route::put('/users/updatePassword', [UserController::class, 'updatePassword'])->name('updatePassword');
});

// Resource
Route::resource('/users', UserController::class);
Route::put('/users/updatePassword', [UserController::class, 'updatePassword'])->name('updatePassword');
Route::resource('/items', ItemController::class);
Route::get('/items/{item}/unit', [ItemController::class, 'getUnit']);
Route::resource('/itemEquipments', ItemController::class);
Route::resource('/facilities', FacilityController::class);
Route::resource('/facilityReservations', FacilityReservationController::class);
Route::resource('/itemRequests', ItemRequestController::class);
Route::get('/itemRequests/actionViewItems/{id}', [ItemRequestController::class, 'actionViewItems'])->name('actionViewItems');
Route::resource('/units', UnitController::class);
Route::put('/itemRequests/updateItemStatus/{itemId}', [ItemRequestController::class, 'updateItemStatus'])->name('itemRequests.updateItemStatus');
