<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Facility::with('facilityInCharge');

        // Filter facilities for Facilities In-charge
        if ($user->role === 'Facilities In-charge') {
            $query->where('facility_in_charge', $user->id);
        }

        $response = $query->get()->map(function ($list, $index) use ($user) {
            $actionView = '<button onclick="view(\'' . $list->id . '\')" type="button" title="View" class="btn btn-secondary"' . ($list->facility_status === 'Under Maintenance' ? ' disabled' : '') . '><i class="fas fa-eye"></i></button>';
            $actionUpdate = '<button onclick="update(\'' . $list->id . '\')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(\'' . $list->id . '\')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';

            if ($user->role === 'Employee') {
                $action = $actionView;
            } else {
                $action = $actionUpdate . $actionDelete;
            }

            $fullname = $this->formatFullName(
                $list->facilityInCharge->first_name,
                $list->facilityInCharge->middle_name,
                $list->facilityInCharge->last_name,
                $list->facilityInCharge->extension_name
            );

            // Add badge based on facility_status
            $badgeClass = match ($list->facility_status) {
                'Available' => 'badge bg-primary', // Blue badge
                'Under Maintenance' => 'badge bg-danger', // Red badge
                'Out of Order' => 'badge bg-warning', // Orange badge
                default => 'badge bg-secondary', // Fallback
            };
            $statusBadge = '<span class="' . $badgeClass . '">' . $list->facility_status . '</span>';

            return [
                'count' => $index + 1,
                'facility_name' => $list->facility_name,
                'facility_description' => $list->facility_description,
                'facility_status' => $statusBadge,
                'facility_in_charge' => $fullname ?? 'Not Assigned',
                'action' => $action,
            ];
        })->toArray();

        return response()->json($response);
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
        // Start transaction
        DB::beginTransaction();

        try {
            // Validation rules
            $validated = $request->validate([
                'facility_name' => 'required|string|max:100|unique:facilities,facility_name',
                'facility_description' => 'nullable|string',
                'facility_status' => 'required|in:Available,Under Maintenance,Out of Order',
                'facility_in_charge' => 'nullable|exists:users,id',
            ]);

            // Create the facility
            $facility = Facility::create($validated);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Facility successfully stored.',
                'facility' => $facility,
            ], 201);
        } catch (ValidationException $e) {
            // Handle validation errors
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to store facility: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store facility. Please try again later.',
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
            // Retrieve the facility
            $facility = Facility::with('facilityInCharge')->findOrFail($id);

            // Commit the transaction if successful
            DB::commit();

            return response()->json($facility, 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to retrieve facility: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve facility. Please try again later.',
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
            // Validation rules
            $validated = $request->validate([
                'facility_name' => 'required|string|max:100|unique:facilities,facility_name,' . $id,
                'facility_description' => 'nullable|string',
                'facility_status' => 'required|in:Available,Under Maintenance,Out of Order',
                'facility_in_charge' => 'nullable|exists:users,id',
            ]);

            // Find and update the facility
            $facility = Facility::findOrFail($id);
            $facility->update($validated);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Facility successfully updated.',
            ], 201);
        } catch (ValidationException $e) {
            // Handle validation errors
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to update facility: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update facility. Please try again later.',
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
            // Find and delete the facility
            $facility = Facility::findOrFail($id);
            $facility->delete();

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Facility successfully deleted.',
            ], 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to delete facility: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete facility. Please try again later.',
            ], 500);
        }
    }
}
