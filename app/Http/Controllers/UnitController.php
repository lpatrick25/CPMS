<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = Unit::all()->map(function ($unit, $index) {
            $actionUpdate = '<button onclick="update(' . "'" . $unit->id . "'" . ')" type="button" title="Update" class="btn btn-secondary"><i class="fas fa-edit"></i></button>';
            $actionDelete = '<button onclick="trash(' . "'" . $unit->id . "'" . ')" type="button" title="Delete" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
            $action = $actionUpdate . $actionDelete;

            return [
                'count' => $index + 1,
                'name' => ucwords(strtolower($unit->name)),
                'description' => $unit->description,
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
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:units,name',
                'description' => 'nullable|string|max:255',
            ], [
                'name.required' => 'Unit name is required.',
                'name.string' => 'Unit name must be a string.',
                'name.max' => 'Unit name cannot exceed 100 characters.',
                'name.unique' => 'This unit name is already taken.',
                'description.string' => 'Description must be a string.',
                'description.max' => 'Description cannot exceed 255 characters.',
            ]);

            $unit = Unit::create($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Unit successfully stored.',
                'unit' => $unit,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store unit: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to store unit. Please try again later.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        DB::beginTransaction();

        try {
            $unit = Unit::findOrFail($id);

            DB::commit();

            return response()->json($unit, 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to retrieve unit: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to retrieve unit. Please try again later.',
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
                'name' => 'required|string|max:100|unique:units,name,' . $id,
                'description' => 'nullable|string|max:255',
            ], [
                'name.required' => 'Unit name is required.',
                'name.string' => 'Unit name must be a string.',
                'name.max' => 'Unit name cannot exceed 100 characters.',
                'name.unique' => 'This unit name is already taken.',
                'description.string' => 'Description must be a string.',
                'description.max' => 'Description cannot exceed 255 characters.',
            ]);

            $unit = Unit::findOrFail($id);
            $unit->update($validated);

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Unit successfully updated.',
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'valid' => false,
                'msg' => '',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update unit: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to update unit. Please try again later.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            DB::commit();

            return response()->json([
                'valid' => true,
                'msg' => 'Unit successfully deleted.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete unit: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'msg' => 'Failed to delete unit. Please try again later.',
            ], 500);
        }
    }
}
