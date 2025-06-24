<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Request as RequestItem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class ItemRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $user_role = $user->role;

        $requestsQuery = RequestItem::with(['itemRequests.item', 'itemRequests.employee']);

        // Filter based on user role
        if ($user_role === 'Employee') {
            $requestsQuery->whereHas('itemRequests', function ($query) use ($user) {
                $query->where('employee_id', $user->id);
            });
        } elseif ($user_role === 'President') {
            // Only show requests with at least one item in 'President Approval' status
            $requestsQuery->whereHas('itemRequests', function ($query) {
                $query->where('status', 'President Approval');
            });
        }

        $requests = $requestsQuery->get()->map(function ($request, $index) use ($user_role) {
            $actionViewItems = '<button onclick="viewItems(' . "'" . $request->id . "'" . ')" type="button" title="View Items" class="btn btn-secondary"><i class="fas fa-eye"></i></button>';
            $actionRelease = '<button onclick="release(' . "'" . $request->id . "'" . ')" type="button" title="Release" class="btn btn-primary"><i class="fas fa-box-open"></i></button>';

            $action = $actionViewItems;

            // Status based on the parent request status
            $status = $request->status;

            if ($status === 'Approved' && $user_role === 'Custodian') {
                $action = $actionRelease;
            }

            // Special case for employees
            if ($user_role === 'Employee') {
                $actionEdit = '<button onclick="update(' . "'" . $request->id . "'" . ')" type="button" title="Edit" class="btn btn-success"><i class="fas fa-edit"></i></button>';
                $actionDelete = '<button onclick="trash(' . "'" . $request->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';

                $action = $actionViewItems;

                if ($status === 'Custodian Approval') {
                    $action = $actionEdit . ' ' . $actionDelete;
                }
            }

            // Format employee name (from the requests table)
            $employee = $request->employee ?? null;
            $fullname = $employee
                ? $this->formatFullName(
                    $employee->first_name ?? '',
                    $employee->middle_name ?? '',
                    $employee->last_name ?? '',
                    $employee->extension_name ?? ''
                )
                : 'Unknown';

            $statusBadge = $this->getStatusBadge($status);

            return [
                'count'             => $index + 1,
                'transaction_number' => $request->transaction_number,
                'employee_name'      => $fullname,
                'date_requested'     => $request->date_requested->format('Y-m-d'),
                'status'            => $statusBadge,
                'action'            => $action,
            ];
        });

        return response()->json($requests);
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

    private function getStatusBadge($status)
    {
        $badges = [
            'Custodian Approval' => '<span class="badge bg-warning text-dark" style="font-size: 1rem; padding: 0.5em 1em;">Pending</span>',
            'President Approval' => '<span class="badge bg-info text-white" style="font-size: 1rem; padding: 0.5em 1em;">Confirmed</span>',
            'Approved' => '<span class="badge bg-success" style="font-size: 1rem; padding: 0.5em 1em;">Approved</span>',
            'Rejected' => '<span class="badge bg-danger" style="font-size: 1rem; padding: 0.5em 1em;">Rejected</span>',
            'Released' => '<span class="badge bg-primary" style="font-size: 1rem; padding: 0.5em 1em;">Released</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary" style="font-size: 1rem; padding: 0.5em 1em;">Unknown</span>';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate input
            $validated = $request->validate([
                'item_id'   => 'required|array',
                'item_id.*' => 'exists:items,id',
                'quantity'  => 'required|array',
                'quantity.*' => 'integer|min:1',
                'employee_id' => 'nullable|exists:users,id',
            ]);

            $employee_id = $request->employee_id ? $request->employee_id : auth()->user()->id;

            $transactionNumber = 'TRX-' . strtoupper(uniqid());

            $requestHeader = \App\Models\Request::create([
                'transaction_number' => $transactionNumber,
                'employee_id'        => $employee_id,
                'date_requested'     => now(),
            ]);

            $itemsProcessed = [];
            $itemDetailsForNotification = [];

            foreach ($request->item_id as $index => $itemId) {
                $quantity = $request->quantity[$index];

                $item = Item::findOrFail($itemId);

                if ($quantity > $item->remaining_quantity) {
                    DB::rollback();
                    return response()->json([
                        'valid' => false,
                        'msg'   => "Insufficient quantity for {$item->name}. Only {$item->remaining_quantity} available.",
                    ], 400);
                }

                ItemRequest::create([
                    'request_id'         => $requestHeader->id,
                    'employee_id'        => $employee_id,
                    'item_id'            => $itemId,
                    'quantity'           => $quantity,
                    'date_requested'     => now(),
                    'status'             => 'Custodian Approval',
                ]);

                $item->decrement('remaining_quantity', $quantity);

                $itemsProcessed[] = $item->name;

                // Collect item details for the notification message
                $itemDetailsForNotification[] = "{$item->name} (x{$quantity})";
            }

            // Notify Custodian(s)
            $custodians = User::where('role', 'Custodian')->get();
            foreach ($custodians as $custodian) {
                Notification::create([
                    'user_id' => $custodian->id,
                    'sender_id' => $employee_id,
                    'type' => 'item_request',
                    'title' => 'New Item Request',
                    'message' => 'A new item request has been made by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name .
                        ' with Transaction Number: ' . $transactionNumber . '. Items: ' . implode(', ', $itemDetailsForNotification),
                    'reference_number' => $transactionNumber,
                ]);
            }

            DB::commit();

            return response()->json([
                'valid'              => true,
                'msg'                => "Item request successfully stored for: " . implode(", ", $itemsProcessed),
                'transaction_number' => $transactionNumber,
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
            Log::error('Failed to store item request: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg'   => 'Failed to store item request. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Start transaction
        DB::beginTransaction();

        try {
            // Retrieve the item request
            $itemRequest = ItemRequest::with(['employee', 'item'])->findOrFail($id);

            // Commit the transaction if successful
            DB::commit();

            return response()->json($itemRequest, 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to retrieve item request: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve item request. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validate that quantities is an array with numeric values
            $validated = $request->validate([
                'quantities' => 'required|array',
                'quantities.*' => 'required|integer|min:1',
            ]);

            // Get all ItemRequest entries related to this Request ID
            $itemRequests = ItemRequest::where('request_id', $id)->get();

            if ($itemRequests->isEmpty()) {
                return response()->json([
                    'valid' => false,
                    'msg' => 'No item requests found for this request ID.',
                ], 404);
            }

            // Loop through requested items to update their quantities
            foreach ($itemRequests as $itemRequest) {
                $itemId = $itemRequest->item_id; // Get related item ID
                if (!isset($validated['quantities'][$itemId])) {
                    continue; // Skip if no new quantity is provided for this item
                }

                $newQuantity = $validated['quantities'][$itemId];
                $item = Item::findOrFail($itemId);

                // If the quantity is increased, check stock availability
                if ($newQuantity > $itemRequest->quantity) {
                    $difference = $newQuantity - $itemRequest->quantity;
                    if ($difference > $item->remaining_quantity) {
                        return response()->json([
                            'valid' => false,
                            'msg' => "The requested quantity for '{$item->name}' exceeds the available stock.",
                        ], 422);
                    }
                    $item->remaining_quantity -= $difference;
                }
                // If the quantity is decreased, return stock
                elseif ($newQuantity < $itemRequest->quantity) {
                    $difference = $itemRequest->quantity - $newQuantity;
                    $item->remaining_quantity += $difference;
                }

                // Update item request's quantity
                $itemRequest->quantity = $newQuantity;
                $itemRequest->save();
                $item->save();
            }

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item request successfully updated.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'valid' => false,
                'msg' => 'Validation error. Please check your inputs.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update item request: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update item request. Please try again later.',
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
            // Find the item request to be deleted
            $itemRequest = ItemRequest::findOrFail($id);

            // Find the related item
            $item = Item::findOrFail($itemRequest->item_id);

            // Update the remaining quantity
            $item->remaining_quantity += $itemRequest->quantity;

            // Save the updated item
            $item->save();

            // Delete the item request
            $itemRequest->delete();

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item request successfully deleted, and quantity updated.',
            ], 200);  // Use HTTP 200 status for success
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to delete item request: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete item request. Please try again later.',
            ], 500);
        }
    }

    public function actionViewItems($id)
    {
        $request = \App\Models\Request::with(['itemRequests.item', 'itemRequests.employee'])
            ->findOrFail($id);

        $employee = $request->employee; // From the 'requests' table
        $status = '';
        if ($request->status === 'Custodian Approval') {
            $status = 'Pending';
        } else if ($request->status === 'President Approval') {
            $status = 'Confirmed';
        } else {
            $status = $request->status;
        }

        return response()->json([

            'date_requested' => $request->date_requested->format('F j, Y'),
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'transaction_number' => $request->transaction_number,
            'status' => $status,
            'date_released' => optional($request->itemRequests->first()->released_at)->format('F j, Y') ?? 'N/A',
            'items' => $request->itemRequests->map(function ($itemRequest) {
                $status = '';
                if ($itemRequest->status === 'Custodian Approval') {
                    $status = 'Pending';
                } else if ($itemRequest->status === 'President Approval') {
                    $status = 'Confirmed';
                } else {
                    $status = $itemRequest->status;
                }
                return [
                    'item_request_id' => $itemRequest->id,
                    'item_id' => $itemRequest->item->id,
                    'item_name' => $itemRequest->item->name ?? 'N/A',
                    'item_description' => $itemRequest->item->description ?? 'N/A',
                    'quantity' => $itemRequest->quantity,
                    'unit' => $itemRequest->item->unit->name ?? 'N/A',
                    'release_quantity' => $itemRequest->release_quantity ?? 'N/A',
                    'status' => $status,
                ];
            }),
        ]);
    }

    public function updateStatus(Request $request, $request_id)
    {
        $validated = $request->validate([
            'status' => 'required|in:President Approval,Approved,Rejected,Released',
            'release_quantities' => 'nullable|array',
            'release_quantities.*' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $userRole = $user->role;
            $newStatus = $validated['status'];

            $itemRequests = ItemRequest::where('request_id', $request_id)->get();
            $requestGroup = RequestItem::findOrFail($request_id);

            if ($itemRequests->isEmpty()) {
                return response()->json(['valid' => false, 'msg' => 'No items found for this request.'], 404);
            }

            // Authorization
            if (
                ($newStatus === 'Approved' && $userRole !== 'President') ||
                ($newStatus === 'Released' && $userRole !== 'Custodian')
            ) {
                return response()->json(['valid' => false, 'msg' => 'You are not authorized to perform this action.'], 403);
            }

            switch ($newStatus) {
                case 'Approved':
                    foreach ($itemRequests as $itemRequest) {
                        if ($itemRequest->status === 'President Approval') {
                            $itemRequest->approved_by_president = $user->id;
                            $itemRequest->status = 'Approved';
                            $itemRequest->save();

                            $this->sendStatusChangeNotification(
                                $itemRequest->employee_id,
                                $user->id,
                                $requestGroup->transaction_number,
                                'Approved',
                                $itemRequest->item->name
                            );
                        }
                    }
                    break;

                case 'Released':
                    $releaseQuantities = $validated['release_quantities'] ?? [];
                    $index = 0;

                    foreach ($itemRequests as $itemRequest) {
                        // Only process items that are approved
                        if ($itemRequest->status === 'Approved') {
                            $releaseQuantity = (int) ($releaseQuantities[$index] ?? 0);

                            if ($releaseQuantity <= 0 || $releaseQuantity > $itemRequest->quantity) {
                                throw new \Exception('Invalid release quantity for item: ' . $itemRequest->id);
                            }

                            $itemRequest->released_by_custodian = $user->id;
                            $itemRequest->release_quantity = $releaseQuantity;
                            $itemRequest->released_at = now();
                            $itemRequest->status = 'Released';
                            $itemRequest->save();

                            $this->sendStatusChangeNotification(
                                $itemRequest->employee_id,
                                $user->id,
                                $requestGroup->transaction_number,
                                'Released',
                                $itemRequest->item->name
                            );

                            $index++; // increment only when we process an approved item
                        }
                        // skip if item not approved
                    }
                    break;
            }

            // Always update the parent status after item status changes
            $this->updateParentRequestStatus($requestGroup);

            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update status: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateItemStatus(Request $request, $requestId)
    {
        $validated = $request->validate([
            'status' => 'required|in:President Approval,Approved,Rejected',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $userRole = $user->role;
            $newStatus = $validated['status'];


            // Find the item request
            $itemRequest = ItemRequest::with('item')->findOrFail($requestId);
            $requestGroup = RequestItem::findOrFail($itemRequest->request_id);

            // Authorization check
            if (
                ($newStatus === 'President Approval' && $userRole !== 'Custodian') ||
                ($newStatus === 'Rejected' && !in_array($userRole, ['Custodian', 'President']))
            ) {
                return response()->json(['valid' => false, 'msg' => 'You are not authorized to perform this action.'], 403);
            }

            // dd($itemRequest->status, $newStatus);

            // Check if the item is in a valid state for the action
            if ($itemRequest->status !== 'Custodian Approval' && $itemRequest->status !== 'President Approval') {
                return response()->json(['valid' => false, 'msg' => 'This item is not in a state that can be updated.'], 400);
            }

            if ($newStatus === 'President Approval') {
                $itemRequest->approved_by_custodian = $user->id;
                $itemRequest->status = 'President Approval';
                $itemRequest->save();

                // Notify President
                $custodian = User::where('role', 'Custodian')->first();
                if ($custodian) {
                    $this->sendStatusChangeNotification(
                        $custodian->id,
                        $user->id,
                        $requestGroup->transaction_number,
                        'Approved',
                        $itemRequest->item->name
                    );
                }
            } elseif ($newStatus === 'Approved') {
                $itemRequest->approved_by_president = $user->id;
                $itemRequest->status = 'Approved';
                $itemRequest->save();

                // Notify President
                $president = User::where('role', 'President')->first();
                if ($president) {
                    $this->sendStatusChangeNotification(
                        $president->id,
                        $user->id,
                        $requestGroup->transaction_number,
                        'Approved',
                        $itemRequest->item->name
                    );
                }
            } elseif ($newStatus === 'Rejected') {
                $itemRequest->status = 'Rejected';
                $item = Item::findOrFail($itemRequest->item_id);
                $item->remaining_quantity += $itemRequest->quantity;
                $item->save();
                $itemRequest->save();

                // Notify employee
                $this->sendStatusChangeNotification(
                    $itemRequest->employee_id,
                    $user->id,
                    $requestGroup->transaction_number,
                    'Rejected',
                    $itemRequest->item->name
                );
            }

            // Update parent request status based on all item requests
            $this->updateParentRequestStatus($requestGroup);

            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'Item status updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update item status: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to update item status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the parent request status based on its item requests
     */
    private function updateParentRequestStatus(RequestItem $requestGroup)
    {
        $itemRequests = ItemRequest::where('request_id', $requestGroup->id)->get();

        // If any item is still in Custodian Approval, keep the request as Custodian Approval
        if ($itemRequests->contains('status', 'Custodian Approval')) {
            $requestGroup->status = 'Custodian Approval';
        }
        // If any item is still in President Approval (and none in Custodian Approval), keep at President Approval
        elseif ($itemRequests->contains('status', 'President Approval')) {
            $requestGroup->status = 'President Approval';
        }
        // If all items are Approved
        elseif ($itemRequests->every(fn($item) => $item->status === 'Approved')) {
            $requestGroup->status = 'Approved';
        }
        // If all items are Released
        elseif ($itemRequests->every(fn($item) => $item->status === 'Released')) {
            $requestGroup->status = 'Released';
        }
        // If any item is Released, set to Released (even if others are Rejected)
        elseif ($itemRequests->contains('status', 'Released')) {
            $requestGroup->status = 'Released';
        }
        // If any item is Approved (and no more pending approvals), set to Approved
        elseif (
            $itemRequests->contains('status', 'Approved') &&
            !$itemRequests->contains('status', 'Custodian Approval') &&
            !$itemRequests->contains('status', 'President Approval')
        ) {
            $requestGroup->status = 'Approved';
        }
        // If all items are Rejected
        elseif ($itemRequests->every(fn($item) => $item->status === 'Rejected')) {
            $requestGroup->status = 'Rejected';
        }

        $requestGroup->save();
    }

    // Method to send notifications
    private function sendStatusChangeNotification($employee_id, $sender_id, $transactionNumber, $status, $itemName = null)
    {
        $itemText = $itemName ? " for item '{$itemName}'" : '';
        $message = 'Your item request with transaction number ' . $transactionNumber . $itemText . ' has been updated to ' . $status;

        if ($status === 'President Approval') {
            $message = 'The item request with transaction number ' . $transactionNumber . $itemText . ' has been approved by the Custodian and is now awaiting your approval.';
        }

        Notification::create([
            'user_id' => $employee_id,
            'sender_id' => $sender_id,
            'type' => 'status_update',
            'title' => 'Item Request Status Updated',
            'message' => $message,
            'reference_number' => $transactionNumber,
        ]);
    }
}
