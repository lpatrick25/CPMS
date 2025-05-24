<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with('unit')->get();

        $response = $items->map(function ($item, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $item->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $item->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate . $actionDelete;

            return [
                'count' => $index + 1,
                'name' => ucwords(strtolower($item->name)),
                'description' => $item->description,
                'unit' => $item->unit ? ucwords(strtolower($item->unit->name)) : 'N/A',
                'quantity' => $item->quantity,
                'remaining_quantity' => $item->remaining_quantity,
                'action' => $action,
            ];
        });

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
                'name' => 'required|string|max:255|unique:items,name',
                'description' => 'required|string|max:255',
                'unit_id' => 'required|exists:units,id',
                'quantity' => 'required|integer|min:0',
            ], [
                'name.required' => 'Item name is required.',
                'name.string' => 'Item name must be a string.',
                'name.max' => 'Item name cannot exceed 255 characters.',
                'name.unique' => 'This item name is already taken.',
                'description.required' => 'Item description is required.',
                'description.string' => 'Item description must be a string.',
                'description.max' => 'Item description cannot exceed 255 characters.',
                'unit_id.required' => 'Item unit is required.',
                'unit_id.exists' => 'Item unit does not exist.',
                'quantity.required' => 'Quantity is required.',
                'quantity.integer' => 'Quantity must be an integer.',
                'quantity.min' => 'Quantity cannot be negative.',
            ]);

            $validated['remaining_quantity'] = $validated['quantity'];

            // Create the item
            $item = Item::create($validated);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item successfully stored.',
                'item' => $item,
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
            Log::error('Failed to store item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store item. Please try again later.',
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
            // Retrieve the item
            $item = Item::findOrFail($id);

            // Commit the transaction if successful
            DB::commit();

            return response()->json($item, 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to retrieve item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve item. Please try again later.',
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
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:items,name,' . $id,
                'description' => 'required|string|max:255',
                'unit_id' => 'required|exists:units,id',
                'quantity' => 'required|integer|min:0',
            ], [
                'name.required' => 'Item name is required.',
                'name.string' => 'Item name must be a string.',
                'name.max' => 'Item name cannot exceed 255 characters.',
                'name.unique' => 'This item name is already taken.',
                'description.required' => 'Item description is required.',
                'description.string' => 'Item description must be a string.',
                'description.max' => 'Item description cannot exceed 255 characters.',
                'unit_id.required' => 'Item unit is required.',
                'unit_id.exists' => 'Item unit does not exist.',
                'quantity.required' => 'Quantity is required.',
                'quantity.integer' => 'Quantity must be an integer.',
                'quantity.min' => 'Quantity cannot be negative.',
            ]);

            // Fetch the item
            $item = Item::findOrFail($id);

            // Calculate the difference in quantity
            $quantityDifference = $validated['quantity'] - $item->quantity;

            // Update remaining quantity accordingly
            $item->remaining_quantity += $quantityDifference;

            // Update item fields
            $item->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'unit_id' => $validated['unit_id'],
                'quantity' => $validated['quantity'],
                'remaining_quantity' => $item->remaining_quantity,
            ]);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item successfully updated.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update item. Please try again later.',
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
            // Find and delete the item
            $item = Item::findOrFail($id);
            $item->delete();

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Item successfully deleted.',
            ], 201);
        } catch (\Exception $e) {
            // Handle general exceptions
            DB::rollback();
            Log::error('Failed to delete item: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete item. Please try again later.',
            ], 500);
        }
    }
}
