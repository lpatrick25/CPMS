<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityReservation;
use App\Models\ItemRequest;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade at the top

class ReportController extends Controller
{
    /**
     * Generate Facility Reservation Report for Facilities In-Charge
     */
    public function facilityReservationReport(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'Facilities In-charge') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Base Query
        $query = FacilityReservation::whereHas('facility', function ($query) use ($user) {
            $query->where('facility_in_charge', $user->id);
        })->where('status', 'Confirmed')->with(['employee', 'facility']);

        // Apply Filters
        if ($request->month) {
            $query->whereMonth('reservation_date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('reservation_date', $request->year);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $report = $query->get();

        $response = $report->map(function ($list, $index) {
            $employee = $list->employee;
            $facility = $list->facility;

            $employeeName = $employee
                ? $this->formatFullName(
                    $employee->first_name,
                    $employee->middle_name,
                    $employee->last_name,
                    $employee->extension_name
                )
                : 'N/A';

            $facilityName = $facility->facility_name ?? 'N/A';
            $reservationDate = $list->reservation_date ? date('F j, Y', strtotime($list->reservation_date)) : 'N/A';
            $reservationTime = ($list->start_time ?? 'N/A') . ' - ' . ($list->end_time ?? 'N/A');
            $statusBadge = ucfirst($list->status);

            // Ensure facility in charge exists before accessing properties
            $facilityInCharge = $facility && $facility->facilityInCharge
                ? $this->formatFullName(
                    $facility->facilityInCharge->first_name,
                    $facility->facilityInCharge->middle_name,
                    $facility->facilityInCharge->last_name,
                    $facility->facilityInCharge->extension_name
                )
                : 'Not Assigned';

            return [
                'count' => $index + 1,
                'employee_name' => $employeeName,
                'facility_name' => $facilityName,
                'reservation_date' => $reservationDate,
                'reservation_time' => $reservationTime,
                'status' => $statusBadge,
                'facility_in_charge' => $facilityInCharge,
            ];
        })->toArray();

        return response()->json($response);
    }

    /**
     * Generate Item Request Report for Custodian
     */
    public function itemRequestReport(Request $request)
    {
        $user = auth()->user();

        // Check if the user is authorized to view the report
        if (!in_array($user->role, ['Custodian', 'President'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = ItemRequest::query();

        // Filter logic based on user role (Custodian sees their actions, President sees all)
        if ($user->role === 'Custodian') {
            $query->where(function ($q) use ($user) {
                $q->where('approved_by_custodian', $user->id)
                    ->orWhere('released_by_custodian', $user->id);
            });
        }

        // Apply optional filters from the request
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $itemRequests = $query->with(['employee', 'item.unit', 'request'])->get();

        $response = $itemRequests->map(function ($itemRequest, $index) {
            $employee = $itemRequest->employee;
            $item = $itemRequest->item;
            $parentRequest = $itemRequest->request;

            // Format the employee name
            $employeeName = $employee
                ? $this->formatFullName(
                    $employee->first_name,
                    $employee->middle_name,
                    $employee->last_name,
                    $employee->extension_name
                )
                : 'N/A';

            // Get the unit name for the item
            $unitName = optional($item->unit)->name ?? 'N/A';

            return [
                'count' => $index + 1,
                'transaction_number' => optional($parentRequest)->transaction_number ?? 'N/A',
                'employee_name' => $employeeName,
                'item_name' => optional($item)->name ?? 'N/A',
                'item_description' => optional($item)->description ?? 'N/A',
                'unit_name' => $unitName,
                'requested_quantity' => ($itemRequest->quantity ?? 0) . ' ' . $unitName,
                'release_quantity' => ($itemRequest->release_quantity ?? 0) . ' ' . $unitName,
                'status' => ucfirst($itemRequest->status),
                'release_date' => optional($itemRequest->released_at)->format('F j, Y') ?? 'N/A',
            ];
        });

        return response()->json($response);
    }

    // 🔹 Function to fetch stock items (Consumables & Tools)
    public function getStockItemsReports(Request $request)
    {
        $user = auth()->user();

        // Corrected Authorization Logic
        if (!in_array($user->role, ['Custodian', 'President'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Optional: Log filters for debugging
        Log::info('Request Filters:', $request->all());

        // Start building the query
        $itemsQuery = Item::with('unit'); // Eager load the unit relationship

        // Filtering based on description
        if ($request->filled('description')) {
            $itemsQuery->where('description', 'LIKE', '%' . $request->description . '%');
        }

        // Filtering based on date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            // Parse and format the dates
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $itemsQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        // Execute the query to get the items
        $items = $itemsQuery->get();

        // Map the items into the desired response format
        $response = $items->map(function ($item, $index) {
            // Get the unit name if available
            $unitName = optional($item->unit)->name ?? 'N/A';

            return [
                'count' => $index + 1,
                'name' => $item->name,
                'description' => $item->description,
                'unit' => $unitName, // Add the unit name
                'initial_quantity' => $item->quantity,
                'remaining_quantity' => $item->remaining_quantity,
            ];
        });

        // Return the response as JSON
        return response()->json($response);
    }

    public function downloadStockItemsReport(Request $request)
    {
        $user = auth()->user();

        // Correct Authorization Logic
        if (!in_array($user->role, ['Custodian', 'President'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Start building the query and eager load unit relation
        $itemsQuery = Item::with('unit'); // Eager load unit information

        // Filtering based on description (using LIKE for partial matching)
        if ($request->filled('description')) {
            $itemsQuery->where('description', 'LIKE', '%' . $request->description . '%');
        }

        // Filtering based on date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $itemsQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        // Execute the query to get items
        $items = $itemsQuery->get();

        // Generate the PDF using the items collection
        $pdf = PDF::loadView('reports.stock_pdf', compact('items'));

        // Download the PDF with a specified filename
        return $pdf->download('stock_items_report.pdf');
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
}
