<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EquipmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->role;

        $equipmentQuery = Equipment::with('serials', 'inCharge');

        // If the user is Custodian or Equipment In-charge, only get assigned items
        if (in_array($userRole, ['Custodian', 'Equipment In-charge'])) {
            $equipmentQuery->where('equipment_in_charge', $user->id);
        }

        $response = $equipmentQuery->get()->map(function ($list, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $list->id . "'" . ')" type="button" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $list->id . "'" . ')" type="button" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate . $actionDelete;

            return [
                'count' => $index + 1,
                'name' => $list->name,
                'description' => $list->description,
                'quantity' => $list->quantity,
                'remaining_quantity' => $list->remaining_quantity,
                'category' => $list->category,
                'has_serial' => $list->has_serial ? 'Yes' : 'No',
                'in_charge' => optional($list->inCharge)->name ?? 'N/A',
                'action' => $action,
            ];
        });

        return response()->json($response);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:equipment,name',
                'description' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1',
                'has_serial' => 'sometimes|boolean',
                'serial_numbers' => 'nullable|array',
                'serial_numbers.*' => 'string|distinct|unique:equipment_serials,serial_number',
                'equipment_in_charge' => 'nullable|exists:users,id',
                'category' => 'required|in:Tool,Equipment',
            ]);

            // If Tool -> has_serial = false, and quantity is required
            if ($validated['category'] === 'Tool') {
                $validated['has_serial'] = false;
                $validated['quantity'] = $validated['quantity'] ?? 1;
                $validated['remaining_quantity'] = $validated['quantity'];
            }

            // If Equipment -> validate serials if has_serial is true
            if ($validated['category'] === 'Equipment') {
                $hasSerial = $validated['has_serial'] ?? false;

                if ($hasSerial) {
                    if (!isset($validated['serial_numbers'])) {
                        throw ValidationException::withMessages(['serial_numbers' => 'Serial numbers are required when has_serial is true.']);
                    }
                    $validated['quantity'] = count($validated['serial_numbers']);
                    $validated['remaining_quantity'] = count($validated['serial_numbers']);
                } else {
                    $validated['quantity'] = $validated['quantity'] ?? 1;
                    $validated['remaining_quantity'] = $validated['quantity'];
                }
            }

            $equipment = Equipment::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'quantity' => $validated['quantity'],
                'remaining_quantity' => $validated['remaining_quantity'],
                'has_serial' => $validated['has_serial'] ?? false,
                'equipment_in_charge' => auth()->user()->id,
                'category' => $validated['category'],
            ]);

            if ($equipment->has_serial && isset($validated['serial_numbers'])) {
                $serialData = array_map(function ($serial) use ($equipment) {
                    return [
                        'equipment_id' => $equipment->id,
                        'serial_number' => $serial,
                        'status' => 'Available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $validated['serial_numbers']);

                EquipmentSerial::insert($serialData);
            }

            DB::commit();

            return response()->json(['valid' => true, 'msg' => 'Equipment created.', 'equipment' => $equipment->load('serials')], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['valid' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'An error occurred.'], 500);
        }
    }

    public function show($id)
    {
        $equipment = Equipment::with('serials')->findOrFail($id);
        return response()->json($equipment);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $equipment = Equipment::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:equipment,name,' . $id,
                'description' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:0',
                'has_serial' => 'sometimes|boolean',
                'serial_numbers' => 'nullable|array',
                'serial_numbers.*' => 'string|distinct',
                'equipment_in_charge' => 'nullable|exists:users,id',
                'category' => 'required|in:Tool,Equipment',
            ]);

            // If changing from Equipment to Tool, delete serials and set has_serial to false
            if ($equipment->category === 'Equipment' && $validated['category'] === 'Tool') {
                $validated['has_serial'] = false;
                $equipment->serials()->delete();
            }

            // If Tool, ensure quantity and no serials
            if ($validated['category'] === 'Tool') {
                $validated['has_serial'] = false;
                $validated['quantity'] = $validated['quantity'] ?? $equipment->quantity;
                $validated['remaining_quantity'] = $equipment->remaining_quantity + (($validated['quantity'] ?? $equipment->quantity) - $equipment->quantity);
            }

            // If Equipment, check serials if has_serial is true
            if ($validated['category'] === 'Equipment') {
                $hasSerial = $validated['has_serial'] ?? $equipment->has_serial;

                if ($hasSerial) {
                    if (!isset($validated['serial_numbers'])) {
                        throw ValidationException::withMessages(['serial_numbers' => 'Serial numbers are required when has_serial is true.']);
                    }
                    $validated['quantity'] = count($validated['serial_numbers']);
                    $validated['remaining_quantity'] = count($validated['serial_numbers']);
                } else {
                    // Equipment without serials - quantity can be adjusted
                    $newQuantity = $validated['quantity'] ?? $equipment->quantity;
                    $quantityDifference = $newQuantity - $equipment->quantity;

                    $validated['quantity'] = $newQuantity;
                    $validated['remaining_quantity'] = $equipment->remaining_quantity + $quantityDifference;

                    if ($validated['remaining_quantity'] < 0) {
                        throw new \Exception('Remaining quantity cannot be negative.');
                    }
                }
            }

            // Update Equipment
            $equipment->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $equipment->description,
                'quantity' => $validated['quantity'],
                'remaining_quantity' => $validated['remaining_quantity'],
                'has_serial' => $validated['has_serial'] ?? false,
                'equipment_in_charge' => $validated['equipment_in_charge'] ?? $equipment->equipment_in_charge,
                'category' => $validated['category'],
            ]);

            // Manage serial numbers only if Equipment and has_serial is true
            if ($validated['category'] === 'Equipment' && ($validated['has_serial'] ?? false)) {
                // Remove existing serials and insert new ones
                $equipment->serials()->delete();

                $serialData = array_map(function ($serial) use ($equipment) {
                    return [
                        'equipment_id' => $equipment->id,
                        'serial_number' => $serial,
                        'status' => 'Available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $validated['serial_numbers']);

                EquipmentSerial::insert($serialData);
            } elseif ($validated['category'] === 'Tool' || !$equipment->has_serial) {
                // If Tool or Equipment without serials, delete all serials
                $equipment->serials()->delete();
            }

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Equipment successfully updated.',
                'equipment' => $equipment->load('serials'),
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
            Log::error('Failed to update equipment: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update equipment. Please try again later.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return response()->json(['valid' => true, 'msg' => 'Equipment deleted.']);
    }
}
