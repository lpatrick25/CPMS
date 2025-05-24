<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentBorrowing;
use App\Models\EquipmentBorrowingSerial;
use App\Models\EquipmentSerial;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class EquipmentBorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $userRole = $user->role;

        $equipmentQuery = EquipmentBorrowing::with(['equipment', 'employee', 'approvedBy', 'releasedBy', 'returnedBy']);

        if ($userRole === 'Employee') {
            // Employee can view their own requests only
            $equipmentQuery->where('employee_id', $userId);
        } elseif (in_array($userRole, ['Equipment In-charge', 'Custodian'])) {
            // Equipment In-charge and Custodian can see the equipment they're responsible for
            $equipmentQuery->whereHas('equipment', function ($query) use ($userId) {
                $query->where('equipment_in_charge', $userId);
            });
        }
        // President can see all requests, so no additional condition is needed

        $response = $equipmentQuery->get()->map(function ($borrowing, $index) use ($userRole) {
            $currentDate = now();
            $dateOfUsage = Carbon::parse($borrowing->date_of_usage);

            // 🔘 Action Buttons
            $actionApprove = '<button onclick="approve(\'' . $borrowing->id . '\')" type="button" title="Approve" class="btn btn-info"><i class="fas fa-check-circle"></i></button>';
            $actionReject = '<button onclick="reject(\'' . $borrowing->id . '\')" type="button" title="Reject" class="btn btn-warning"><i class="fas fa-times-circle"></i></button>';
            $actionRelease = '<button onclick="release(\'' . $borrowing->id . '\', ' . ($borrowing->equipment->has_serial ? 'true' : 'false') . ', ' . $borrowing->quantity . ')" type="button" title="Release" class="btn btn-primary"><i class="fas fa-box-open"></i></button>';
            $actionCantRelease = '<button type="button" title="Cannot Release" class="btn btn-secondary disabled"><i class="fas fa-box-open"></i> Cannot Release Yet</button>';
            $actionReturned = '<button onclick="markReturned(\'' . $borrowing->id . '\')" type="button" title="Returned" class="btn btn-success"><i class="fas fa-undo"></i> Returned</button>';

            $action = '';

            // 🛠️ Determine Actions Based on Status and User Role
            switch ($borrowing->status) {
                case 'In-charge Approval':
                    if (in_array($userRole, ['Equipment In-charge', 'Custodian'])) {
                        $action = $actionApprove . ' ' . $actionReject;
                    }
                    break;

                case 'President Approval':
                    if ($userRole === 'President') {
                        $action = $actionApprove . ' ' . $actionReject;
                    }
                    break;

                case 'Approved':
                    if (in_array($userRole, ['Equipment In-charge', 'Custodian'])) {
                        $action = ($currentDate >= $dateOfUsage) ? $actionRelease : $actionCantRelease;
                    }
                    break;

                case 'Released':
                    if (in_array($userRole, ['Equipment In-charge', 'Custodian'])) {
                        $action = $actionReturned;
                    }
                    break;
            }

            // 🧑‍💼 Employee Full Name
            $employeeFullName = $this->formatFullName(
                $borrowing->employee->first_name ?? '',
                $borrowing->employee->middle_name ?? '',
                $borrowing->employee->last_name ?? '',
                $borrowing->employee->extension_name ?? ''
            );

            // 🏷️ Status Badge
            $statusBadge = $this->getStatusBadge($borrowing->status);

            return [
                'count' => $index + 1,
                'transaction_number' => $borrowing->transaction_number,
                'employee_name' => $employeeFullName ?? 'Unknown',
                'equipment_name' => $borrowing->equipment->name ?? 'Unknown',
                'quantity' => $borrowing->quantity . ' ' . ($borrowing->equipment->unit ?? ''),
                'status' => $statusBadge,
                'date_of_usage' => date('F j, Y', strtotime($borrowing->date_of_usage)),
                'date_of_return' => date('F j, Y', strtotime($borrowing->date_of_return)),
                'action' => $action,
            ];
        })->toArray();

        return response()->json($response);
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'In-charge Approval' => '<span class="badge bg-warning text-dark" style="font-size: 1rem; padding: 0.5em 1em;">Pending</span>',
            'President Approval' => '<span class="badge bg-info text-white" style="font-size: 1rem; padding: 0.5em 1em;">Confirmed</span>',
            'Approved' => '<span class="badge bg-success" style="font-size: 1rem; padding: 0.5em 1em;">Approved</span>',
            'Rejected' => '<span class="badge bg-danger" style="font-size: 1rem; padding: 0.5em 1em;">Rejected</span>',
            'Released' => '<span class="badge bg-primary" style="font-size: 1rem; padding: 0.5em 1em;">Released</span>',
            'Returned' => '<span class="badge bg-secondary" style="font-size: 1rem; padding: 0.5em 1em;">Returned</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-dark" style="font-size: 1rem; padding: 0.5em 1em;">Unknown</span>';
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'equipment_id'      => 'required|array',
                'equipment_id.*'    => 'exists:equipment,id',
                'quantity'          => 'required|array',
                'quantity.*'        => 'integer|min:1',
                'date_of_usage'     => 'required|array',
                'date_of_usage.*'   => 'date|after_or_equal:today',
                'date_of_return'    => 'required|array',
                'date_of_return.*'  => 'date',
            ]);

            $borrowedItems = [];
            $employeeId = auth()->user()->id;

            foreach ($validated['equipment_id'] as $index => $equipmentId) {
                $quantity = $validated['quantity'][$index];
                $dateOfUsage = $validated['date_of_usage'][$index];
                $dateOfReturn = $validated['date_of_return'][$index];

                if (strtotime($dateOfReturn) < strtotime($dateOfUsage)) {
                    DB::rollback();
                    return response()->json([
                        'valid' => false,
                        'msg'   => "The return date must be after or equal to the usage date for Equipment ID {$equipmentId}.",
                    ], 400);
                }

                $equipment = Equipment::findOrFail($equipmentId);

                if ($quantity > $equipment->remaining_quantity) {
                    DB::rollback();
                    return response()->json([
                        'valid' => false,
                        'msg'   => "Insufficient quantity for {$equipment->name}. Only {$equipment->remaining_quantity} available.",
                    ], 400);
                }

                $transactionNumber = 'TRX-' . strtoupper(uniqid());

                $borrowing = EquipmentBorrowing::create([
                    'transaction_number' => $transactionNumber,
                    'employee_id'        => $employeeId,
                    'equipment_id'       => $equipmentId,
                    'quantity'           => $quantity,
                    'date_of_usage'      => $dateOfUsage,
                    'date_of_return'     => $dateOfReturn,
                ]);

                // Handle equipment without serial numbers
                if (!$equipment->has_serial) {
                    $equipment->remaining_quantity -= $quantity;
                    $equipment->save();
                } else {
                    // Handle equipment with serial numbers
                    $serials = $equipment->serials()->where('status', 'Available')->take($quantity)->get();

                    if ($serials->count() < $quantity) {
                        DB::rollback();
                        return response()->json([
                            'valid' => false,
                            'msg'   => "Not enough available serials for {$equipment->name}. Only {$serials->count()} available.",
                        ], 400);
                    }

                    // Update the serials to 'Borrowed'
                    foreach ($serials as $serial) {
                        $serial->update(['status' => 'Borrowed']);
                    }

                    // Decrease remaining quantity only if serials are being borrowed
                    $equipment->remaining_quantity -= $quantity;
                    $equipment->save();
                }

                $borrowedItems[] = $equipment->name;

                // 🛎️ Notify the specific Equipment In-charge (custodian/equipment in-charge)
                if ($equipment->equipment_in_charge) {
                    $this->sendBorrowingNotification(
                        $employeeId,
                        $equipment->equipment_in_charge,
                        $transactionNumber,
                        $equipment->name
                    );
                }
            }

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg'   => "Equipment borrowing successfully stored for: " . implode(", ", $borrowedItems),
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'valid'  => false,
                'msg'    => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store equipment borrowing: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'msg'   => 'Failed to store equipment borrowing. Please try again later.',
            ], 500);
        }
    }

    private function sendBorrowingNotification($employeeId, $inChargeId, $transactionNumber, $equipmentName)
    {
        Notification::create([
            'user_id'          => $inChargeId,       // Notify the specific Equipment In-charge
            'sender_id'        => $employeeId,       // The employee who made the borrowing request
            'type'             => 'equipment_borrowing',
            'title'            => 'New Equipment Borrowing Request',
            'message'          => "A new equipment borrowing request has been submitted for '{$equipmentName}' with Transaction #{$transactionNumber}.",
            'reference_number' => $transactionNumber,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        DB::beginTransaction();

        try {
            // Retrieve the borrowing record with related data
            $borrowing = EquipmentBorrowing::with(['equipment', 'employee', 'approvedBy', 'releasedBy', 'returnedBy'])
                ->findOrFail($id);

            // Commit the transaction if successful
            DB::commit();

            return response()->json($borrowing, 200);  // Ensure this includes all necessary fields
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve equipment borrowing: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve equipment borrowing. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Start transaction
        DB::beginTransaction();

        try {
            // Validation rules including date fields
            $validated = $request->validate([
                'equipment_id' => 'required|exists:equipment,id',
                'quantity' => 'required|integer|min:1',
                'date_of_usage' => 'required|date',
                'date_of_return' => 'required|date',
            ]);

            // Find and update the borrowing record
            $borrowing = EquipmentBorrowing::findOrFail($id);

            // Check if quantity is changed and validate available equipment (Optional: If your business logic requires this)
            if ($validated['quantity'] !== $borrowing->quantity) {
                $equipment = Equipment::findOrFail($validated['equipment_id']);
                if ($validated['quantity'] > $equipment->quantity) {
                    return response()->json([
                        'valid' => false,
                        'msg' => 'Requested quantity exceeds available stock.',
                    ], 422);
                }
            }

            // Update the borrowing record with validated data
            $borrowing->update([
                'equipment_id' => $validated['equipment_id'],
                'quantity' => $validated['quantity'],
                'date_of_usage' => $validated['date_of_usage'],
                'date_of_return' => $validated['date_of_return'],
            ]);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment borrowing successfully updated.',
            ], 200);
        } catch (ValidationException $e) {
            // Handle validation errors
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to update equipment borrowing: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update equipment borrowing. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Start transaction
        DB::beginTransaction();

        try {
            // Find and delete the equipment borrowing record
            $borrowing = EquipmentBorrowing::findOrFail($id);
            $borrowing->delete();

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment borrowing successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to delete equipment borrowing: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete equipment borrowing. Please try again later.',
            ], 500);
        }
    }

    public function getAvailableSerials($requestId)
    {
        $borrowing = EquipmentBorrowing::findOrFail($requestId);

        $availableSerials = EquipmentSerial::where('equipment_id', $borrowing->equipment_id)
            ->where('status', 'Available')
            ->get();

        return response()->json(['serials' => $availableSerials]);
    }

    public function releaseWithSerials(Request $request, $id)
    {
        $borrowing = EquipmentBorrowing::findOrFail($id);
        $serialIds = $request->input('serials', []);
        $user = auth()->user();

        if (count($serialIds) != $borrowing->quantity) {
            return response()->json(['valid' => false, 'msg' => 'Selected serials do not match the requested quantity.'], 400);
        }

        DB::transaction(function () use ($borrowing, $serialIds, $user) {
            foreach ($serialIds as $serialId) {
                EquipmentBorrowingSerial::create([
                    'borrowing_id' => $borrowing->id,
                    'serial_id' => $serialId,
                ]);

                EquipmentSerial::where('id', $serialId)->update(['status' => 'Borrowed']);
            }

            $borrowing->update([
                'status' => 'Released',
                'released_by' => $user->id,
                'released_at' => now(),
            ]);

            // ✅ Send Notification to Employee
            $this->sendStatusChangeNotification(
                $borrowing->employee_id,
                $user->id,
                $borrowing->transaction_number,
                'Released',
                "The requested equipment (Serials) has been released. Transaction #{$borrowing->transaction_number}."
            );
        });

        return response()->json(['valid' => true, 'msg' => 'Equipment released successfully.']);
    }

    public function updateBorrowingStatus(Request $request, $borrowing_id)
    {
        $validated = $request->validate([
            'status' => 'required|in:In-charge Approval,President Approval,Approved,Rejected,Released,Returned',
        ]);

        DB::beginTransaction();

        try {
            $itemRequest = EquipmentBorrowing::findOrFail($borrowing_id);
            $currentStatus = $itemRequest->status;
            $newStatus = $validated['status'];

            $user = auth()->user();
            $userRole = $user->role;
            $item = Equipment::findOrFail($itemRequest->equipment_id);

            switch ($newStatus) {
                case 'Rejected':
                    if (!in_array($userRole, ['Custodian', 'Equipment In-charge', 'President'])) {
                        return response()->json(['valid' => false, 'msg' => 'Unauthorized.'], 403);
                    }
                    if (in_array($currentStatus, ['In-charge Approval', 'President Approval'])) {
                        $item->remaining_quantity += $itemRequest->quantity;
                        $item->save();
                    }
                    break;

                case 'President Approval':
                    if ($currentStatus !== 'In-charge Approval') {
                        return response()->json(['valid' => false, 'msg' => 'Invalid status transition.'], 400);
                    }
                    if (!in_array($userRole, ['Custodian', 'Equipment In-charge'])) {
                        return response()->json(['valid' => false, 'msg' => 'Unauthorized.'], 403);
                    }
                    break;

                case 'Approved':
                    if ($currentStatus !== 'President Approval') {
                        return response()->json(['valid' => false, 'msg' => 'Invalid status transition.'], 400);
                    }
                    if ($userRole !== 'President') {
                        return response()->json(['valid' => false, 'msg' => 'Unauthorized.'], 403);
                    }
                    $itemRequest->approved_by = $user->id;
                    break;

                case 'Released':
                    if ($currentStatus !== 'Approved') {
                        return response()->json(['valid' => false, 'msg' => 'Invalid status transition.'], 400);
                    }
                    if (!in_array($userRole, ['Custodian', 'Equipment In-charge'])) {
                        return response()->json(['valid' => false, 'msg' => 'Unauthorized.'], 403);
                    }
                    $itemRequest->released_by = $user->id;
                    $itemRequest->released_at = now();

                    if (!$item->has_serial) {
                        $item->remaining_quantity -= $itemRequest->quantity;
                        if ($item->remaining_quantity < 0) {
                            return response()->json(['valid' => false, 'msg' => 'Insufficient stock.'], 400);
                        }
                        $item->save();
                    }
                    break;

                case 'Returned':
                    if ($currentStatus !== 'Released') {
                        return response()->json(['valid' => false, 'msg' => 'Invalid status transition.'], 400);
                    }
                    if (!in_array($userRole, ['Custodian', 'Equipment In-charge'])) {
                        return response()->json(['valid' => false, 'msg' => 'Unauthorized.'], 403);
                    }

                    $itemRequest->returned_by = $user->id;
                    $itemRequest->returned_at = now();

                    if (!$item->has_serial) {
                        $item->remaining_quantity += $itemRequest->quantity;
                        $item->save();
                    } else {
                        // ✅ Return Serial Status Update
                        $borrowedSerials = EquipmentBorrowingSerial::where('borrowing_id', $itemRequest->id)->get();

                        foreach ($borrowedSerials as $borrowedSerial) {
                            EquipmentSerial::where('id', $borrowedSerial->serial_id)
                                ->update(['status' => 'Available']);
                        }
                    }
                    break;

                default:
                    return response()->json(['valid' => false, 'msg' => 'Invalid status.'], 400);
            }

            $itemRequest->status = $newStatus;
            $itemRequest->save();

            // ✅ Notification Handling for Status Updates
            $notificationMessages = [
                'President Approval' => 'A new equipment borrowing request is awaiting your approval.',
                'Approved' => 'Your borrowing request is approved and ready for release.',
                'Rejected' => 'Your borrowing request has been rejected.',
                'Released' => 'The requested equipment has been released.',
                'Returned' => 'The borrowed equipment has been returned.',
            ];

            if (isset($notificationMessages[$newStatus])) {
                $this->sendStatusChangeNotification(
                    $itemRequest->employee_id,
                    $user->id,
                    $itemRequest->transaction_number,
                    $newStatus,
                    "{$notificationMessages[$newStatus]} Transaction #{$itemRequest->transaction_number}."
                );
            }

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => "Status updated to '{$newStatus}' successfully.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update status: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update status. Please try again later.',
            ], 500);
        }
    }

    /**
     * Send a status change notification.
     */
    private function sendStatusChangeNotification($recipient_id, $sender_id, $transactionNumber, $status)
    {
        // Default status update messages
        $statusMessages = [
            'President Approval' => 'A new equipment borrowing request is awaiting your approval.',
            'Approved' => 'Your borrowing request is approved and ready for release.',
            'Rejected' => 'Your borrowing request has been rejected.',
            'Released' => 'The requested equipment has been released.',
            'Returned' => 'The borrowed equipment has been returned.',
        ];

        // Use the default message or pass a custom one
        $message = $statusMessages[$status] ?? "Your borrowing request with Transaction #{$transactionNumber} is now '{$status}'.";

        Notification::create([
            'user_id' => $recipient_id,
            'sender_id' => $sender_id,
            'type' => 'status_update',
            'title' => 'Equipment Borrowing Status Updated',
            'message' => $message,
            'reference_number' => $transactionNumber,
        ]);
    }
}
