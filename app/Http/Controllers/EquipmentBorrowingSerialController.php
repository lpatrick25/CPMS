<?php

namespace App\Http\Controllers;

use App\Models\EquipmentBorrowingSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class EquipmentBorrowingSerialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = EquipmentBorrowingSerial::with(['borrowing', 'serial'])
            ->get()
            ->map(function ($borrowingSerial, $index) {
                $actionDelete = '<button onclick="trash(' . "'" . $borrowingSerial->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
                $action = $actionDelete;

                return [
                    'count' => $index + 1,
                    'transaction_number' => $borrowingSerial->borrowing->transaction_number ?? 'N/A',
                    'serial_number' => $borrowingSerial->serial->serial_number ?? 'N/A',
                    'status' => $borrowingSerial->serial->status ?? 'N/A',
                    'action' => $action,
                ];
            })->toArray();

        return response()->json($response);
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
                'borrowing_id' => 'required|exists:equipment_borrowings,id',
                'serial_id' => 'required|exists:equipment_serials,id',
            ], [
                'borrowing_id.required' => 'Borrowing ID is required.',
                'serial_id.required' => 'Serial ID is required.',
            ]);

            // Create the equipment borrowing serial record
            $borrowingSerial = EquipmentBorrowingSerial::create($validated);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment borrowing serial successfully stored.',
                'borrowing_serial' => $borrowingSerial,
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
            Log::error('Failed to store equipment borrowing serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store equipment borrowing serial. Please try again later.',
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
            // Retrieve the borrowing serial record
            $borrowingSerial = EquipmentBorrowingSerial::with(['borrowing', 'serial'])
                ->findOrFail($id);

            // Commit the transaction if successful
            DB::commit();

            return response()->json($borrowingSerial, 200);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to retrieve equipment borrowing serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve equipment borrowing serial. Please try again later.',
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
            // Find and delete the equipment borrowing serial record
            $borrowingSerial = EquipmentBorrowingSerial::findOrFail($id);
            $borrowingSerial->delete();

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment borrowing serial successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to delete equipment borrowing serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete equipment borrowing serial. Please try again later.',
            ], 500);
        }
    }
}
