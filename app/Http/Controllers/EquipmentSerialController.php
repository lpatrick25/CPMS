<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class EquipmentSerialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = EquipmentSerial::with('equipment')->get()->map(function ($serial, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $serial->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $serial->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate . $actionDelete;

            return [
                'count' => $index + 1,
                'equipment_name' => $serial->equipment->name ?? 'N/A',
                'serial_number' => $serial->serial_number,
                'status' => $serial->status,
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
                'equipment_id' => 'required|exists:equipment,id',
                'serial_number' => 'required|string|max:255|unique:equipment_serials,serial_number',
                'status' => 'required|in:Available,Borrowed,Under Maintenance,Retired',
            ]);

            // Create the equipment serial
            $serial = EquipmentSerial::create($validated);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment serial successfully stored.',
                'equipment_serial' => $serial,
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
            Log::error('Failed to store equipment serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store equipment serial. Please try again later.',
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
            // Retrieve the equipment serial
            $serial = EquipmentSerial::with('equipment')->findOrFail($id);

            // Commit the transaction if successful
            DB::commit();

            return response()->json($serial, 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to retrieve equipment serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve equipment serial. Please try again later.',
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
                'equipment_id' => 'required|exists:equipment,id',
                'serial_number' => 'required|string|max:255|unique:equipment_serials,serial_number,' . $id,
                'status' => 'required|in:Available,Borrowed,Under Maintenance,Retired',
            ]);

            // Find and update the equipment serial
            $serial = EquipmentSerial::findOrFail($id);
            $serial->update($validated);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment serial successfully updated.',
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
            Log::error('Failed to update equipment serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update equipment serial. Please try again later.',
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
            // Find and delete the equipment serial
            $serial = EquipmentSerial::findOrFail($id);
            $serial->delete();

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment serial successfully deleted.',
            ], 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to delete equipment serial: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete equipment serial. Please try again later.',
            ], 500);
        }
    }
}
