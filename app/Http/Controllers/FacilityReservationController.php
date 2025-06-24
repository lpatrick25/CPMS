<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FacilityReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the logged-in user's role
        $authUserRole = auth()->user()->role;

        // Retrieve all reservations with related facility and employee.
        $facilityReservations = FacilityReservation::with(['facility', 'employee'])
            ->orderByRaw("FIELD(status, 'Pending', 'Confirmed', 'Approved', 'Denied')")
            ->get();

        // Map the reservations to the desired format.
        $reservations = $facilityReservations->map(function ($reservation, $index) use ($authUserRole) {
            // Prepare status badge and action buttons
            $statusBadge = '';
            $actionUpdate = '';
            $actionDelete = '';

            // Check status and the logged-in user's role to decide action buttons visibility
            switch ($reservation->status) {
                case 'Pending':
                    $statusBadge = '<span class="badge bg-warning">Pending</span>';

                    // If the role is 'Facility In-charge', show 'Confirm' and 'Deny' buttons
                    if ($authUserRole === 'Facilities In-charge') {
                        $actionUpdate = '<button onclick="confirmed(' . "'" . $reservation->id . "'" . ')" type="button" title="Confirm" class="btn btn-success"><i class="fas fa-check-circle"></i></button>';
                        $actionDelete = '<button onclick="denied(' . "'" . $reservation->id . "'" . ')" type="button" title="Deny" class="btn btn-danger"><i class="fas fa-times-circle"></i></button>';
                    }
                    break;
                case 'Confirmed':
                    $statusBadge = '<span class="badge bg-success">Confirmed</span>';

                    // If the role is 'Facilities In-charge', disable buttons
                    if ($authUserRole === 'Facilities In-charge') {
                        $actionUpdate = '<button disabled type="button" title="Confirmed" class="btn btn-success"><i class="fas fa-check-circle"></i></button>';
                        $actionDelete = '<button disabled type="button" title="Denied" class="btn btn-danger"><i class="fas fa-times-circle"></i></button>';
                    }
                    // If the role is 'President', show 'Approve' and 'Deny' buttons
                    if ($authUserRole === 'President') {
                        $actionUpdate = '<button onclick="approve(' . "'" . $reservation->id . "'" . ')" type="button" title="Approve" class="btn btn-primary"><i class="fas fa-thumbs-up"></i></button>';
                        $actionDelete = '<button onclick="denied(' . "'" . $reservation->id . "'" . ')" type="button" title="Deny" class="btn btn-danger"><i class="fas fa-times-circle"></i></button>';
                    }
                    break;
                case 'Approved':
                    $statusBadge = '<span class="badge bg-primary">Approved</span>';

                    // Disable buttons for all roles if the reservation is 'Approved'
                    $actionUpdate = '<button disabled type="button" title="Approved" class="btn btn-primary"><i class="fas fa-thumbs-up"></i></button>';
                    $actionDelete = '<button disabled type="button" title="Denied" class="btn btn-danger"><i class="fas fa-times-circle"></i></button>';
                    break;
                case 'Denied':
                    $statusBadge = '<span class="badge bg-danger">Denied</span>';

                    // Disable buttons for all roles if the reservation is 'Denied'
                    $actionUpdate = '<button disabled type="button" title="Denied" class="btn btn-danger"><i class="fas fa-times-circle"></i></button>';
                    $actionDelete = '<button disabled type="button" title="Denied" class="btn btn-danger"><i class="fas fa-times-circle"></i></button>';
                    break;
            }

            // Prepare employee name
            $employee = $reservation->employee;
            $employeeName = $employee
                ? trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name} {$employee->extension_name}")
                : 'N/A';

            // Prepare the response with updated button actions
            return [
                'count' => $index + 1,
                'facility_name' => $reservation->facility->facility_name ?? 'N/A',
                'employee_name' => $employeeName,
                'reservation_date' => date('F j, Y', strtotime($reservation->reservation_date)),
                'reservation_time' => date('h:i A', strtotime($reservation->start_time)) . ' - ' . date('h:i A', strtotime($reservation->end_time)),
                'purpose' => $reservation->purpose,
                'status' => $statusBadge,
                'action' => $actionUpdate . $actionDelete,
            ];
        });

        return response()->json($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate request
            $validated = $request->validate([
                'facility_id' => 'required|exists:facilities,id',
                'employee_id' => 'required|exists:users,id',
                'reservation_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'purpose' => 'required|string|max:500', // Added purpose validation
            ]);

            // Check for time conflicts (Best practice condition)
            $isReserved = FacilityReservation::where('facility_id', $validated['facility_id'])
                ->where('reservation_date', $validated['reservation_date'])
                ->where(function ($query) use ($validated) {
                    $query->whereRaw('? < end_time AND ? > start_time', [$validated['start_time'], $validated['end_time']]);
                })
                ->exists();

            if ($isReserved) {
                return response()->json([
                    'valid' => false,
                    'msg' => 'The facility is already reserved at the specified date and time.',
                ], 409);
            }

            // Create reservation with default status
            $reservation = FacilityReservation::create(array_merge($validated, ['status' => 'Pending']));

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Reservation successfully created.',
                'reservation' => $reservation,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['valid' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store reservation: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to store reservation. Please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $facility_id)
    {
        try {
            $user_id = $request->user_id;

            // Validate if facility exists
            $facility = Facility::findOrFail($facility_id);

            // Fetch reservations for the facility and user
            $reservations = FacilityReservation::with('employee')
                ->where('facility_id', $facility_id)
                ->where(function ($query) use ($user_id) {
                    $query->where('employee_id', $user_id)
                        ->orWhereIn('status', ['Confirmed', 'Approved']);
                })
                ->where(function ($query) use ($user_id) {
                    $query->where('status', '!=', 'Denied')
                        ->orWhere('employee_id', $user_id);
                })
                ->get();

            // Map reservations for frontend
            $mappedReservations = $reservations->map(function ($reservation) use ($user_id, $facility) {
                $isUserReservation = $reservation->employee_id == $user_id;
                $isEditable = $isUserReservation && !in_array($reservation->status, ['Confirmed', 'Approved']);

                // Handle employee name safely (in case of null fields)
                $employee = $reservation->employee;
                $employeeName = $employee
                    ? trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name} {$employee->extension_name}")
                    : 'Unknown Employee';

                // Build readable status label
                $statusLabel = "";
                if ($isUserReservation) {
                    $statusLabel = match ($reservation->status) {
                        'Pending' => 'Your Reservation - Pending',
                        'Confirmed' => 'Your Reservation - Reserved',
                        'Approved' => 'Your Reservation - Approved',
                        default => 'Your Reservation - ' . $reservation->status,
                    };
                } else {
                    $statusLabel = "{$employeeName} - Reserved";
                }

                return [
                    'id' => $reservation->id,
                    'facility_name' => $facility->facility_name,
                    'employee_name' => $employeeName,
                    'reservation_date' => $reservation->reservation_date,
                    'start_time' => $reservation->start_time,
                    'end_time' => $reservation->end_time,
                    'purpose' => $reservation->purpose,
                    'status' => $reservation->status,
                    'status_label' => $statusLabel,
                    'is_user_reservation' => $isUserReservation,
                    'is_editable' => $isEditable,
                ];
            });

            return response()->json([
                'valid' => true,
                'reservations' => $mappedReservations,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve reservations for facility ' . $facility_id . ': ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve reservations. Please try again later.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $reservation_id)
    {
        DB::beginTransaction();

        try {
            // Convert reservation_date to Y-m-d format
            $request['reservation_date'] = date('Y-m-d', strtotime($request->reservation_date));

            // Validate incoming request data
            $validated = $request->validate([
                'reservation_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'status' => 'in:Pending,Confirmed,Approved,Denied',
                'purpose' => 'required|string|max:500', // Added purpose validation
            ]);

            $reservation = FacilityReservation::findOrFail($reservation_id);

            // Time conflict check: Handles all overlap cases
            $isConflict = FacilityReservation::where('facility_id', $reservation->facility_id)
                ->where('reservation_date', $validated['reservation_date'])
                ->where('id', '!=', $reservation->id)
                ->where(function ($query) use ($validated) {
                    $query->where(function ($q) use ($validated) {
                        $q->where('start_time', '<', $validated['end_time'])
                            ->where('end_time', '>', $validated['start_time']);
                    });
                })
                ->exists();

            if ($isConflict) {
                return response()->json([
                    'valid' => false,
                    'msg' => 'The facility is already reserved at the specified date and time.',
                ], 409);
            }

            // Update reservation with validated data
            $reservation->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Reservation successfully updated.',
                'reservation' => $reservation,
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'valid' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update reservation: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update reservation. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($reservation_id)
    {
        DB::beginTransaction();

        try {
            $reservation = FacilityReservation::findOrFail($reservation_id);
            $reservation->delete();
            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'Reservation successfully deleted.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete reservation: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to delete reservation. Please try again later.'], 500);
        }
    }

    /**
     * Update the status of a reservation.
     */
    public function updateStatus(Request $request, $reservation_id)
    {
        DB::beginTransaction();

        try {
            // Validate incoming request data for status update
            $validated = $request->validate([
                'status' => 'required|in:Pending,Confirmed,Approved,Denied',
            ]);

            // Find the existing reservation
            $reservation = FacilityReservation::findOrFail($reservation_id);

            // Prepare data for update
            $updateData = ['status' => $validated['status']];

            // Set 'approved_by' only if status is 'Approved'
            if ($validated['status'] === 'Approved') {
                $updateData['approved_by'] = auth()->user()->id;
            }

            // Update reservation status and 'approved_by'
            $reservation->update($updateData);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Reservation successfully ' . strtolower($validated['status']) . '.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'valid' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update reservation status: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update reservation status. Please try again later.',
            ], 500);
        }
    }
}
