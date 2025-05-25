<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::where('role', '!=', 'System Admin')->get()->map(function ($user, $index) {
            return [
                'count' => $index + 1,
                'fullname' => ucwords("{$user->first_name} {$user->middle_name} {$user->last_name} {$user->extension_name}"),
                'email' => $user->email,
                'contact_no' => $user->contact_no,
                'role' => ucwords($user->role),
                'department' => strtoupper($user->department),
                'actions' => '<button onclick="update(' . "'" . $user->id . "'" . ')" class="btn btn-secondary">Edit</button>
                              <button onclick="disabled(' . "'" . $user->id . "'" . ')" class="btn btn-primary">Disable</button>',
            ];
        });

        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'middle_name' => 'nullable|string|max:50',
                'last_name' => 'required|string|max:50',
                'extension_name' => 'nullable|string|max:6',
                'email' => 'required|string|email|max:50|unique:users,email',
                'contact_no' => 'required|string|regex:/^[0-9]{11}$/|unique:users,contact_no',
                'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*\d).+$/',
                'role' => 'required|in:Custodian,President,Facilities In-charge,Employee,System Admin',
                'department' => 'nullable|string|max:50',
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'],
                'email' => $validated['email'],
                'contact_no' => $validated['contact_no'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'department' => $validated['department'],
            ]);

            return response()->json(['valid' => true, 'msg' => 'User successfully added.', 'user' => $user], 201);
        } catch (ValidationException $e) {
            return response()->json(['valid' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to store user: ' . $e->getMessage());
            return response()->json(['valid' => false, 'msg' => 'Failed to add user. Please try again later.'], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['valid' => false, 'msg' => 'User not found.'], 404);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'middle_name' => 'nullable|string|max:50',
                'last_name' => 'required|string|max:50',
                'extension_name' => 'nullable|string|max:6',
                'email' => 'required|string|email|max:50|unique:users,email,' . $id,
                'contact_no' => 'required|string|regex:/^[0-9]{11}$/|unique:users,contact_no,' . $id,
                'role' => 'required|in:Custodian,President,Facilities In-charge,Employee,System Admin',
                'department' => 'nullable|string|max:50',
            ]);

            $user->update($validated);

            return response()->json(['valid' => true, 'msg' => 'User successfully updated.', 'user' => $user], 200);
        } catch (ValidationException $e) {
            return response()->json(['valid' => false, 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['valid' => true, 'msg' => 'User successfully deleted.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['valid' => false, 'msg' => 'User not found.'], 404);
        }
    }

    /**
     * Log the user in.
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8',
            ]);

            if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
                return response()->json(['valid' => true, 'msg' => 'Login successful', 'user' => Auth::user()], 200);
            }

            return response()->json(['valid' => false, 'msg' => 'Invalid credentials'], 401);
        } catch (ValidationException $e) {
            return response()->json(['valid' => false, 'errors' => $e->errors()], 422);
        }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('viewLogin');
        // return response()->json(['valid' => true, 'msg' => 'Logged out successfully.'], 200);
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'middle_name' => 'nullable|string|max:50',
                'last_name' => 'required|string|max:50',
                'extension_name' => 'nullable|string|max:6',
                'email' => 'required|string|email|max:50|unique:users,email,' . $user->id,
                'contact_no' => 'required|string|regex:/^[0-9]{11}$/|unique:users,contact_no,' . $user->id,
            ]);

            $user->update($validated);

            return response()->json([
                'valid' => true,
                'msg' => 'Profile successfully updated.',
                'user' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'valid' => false,
                'msg' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'msg' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[A-Z]/', // At least one uppercase
                    'regex:/[0-9]/', // At least one number
                    'regex:/[@$!%*?&#]/', // At least one special character
                    'confirmed'
                ],
            ]);

            // Check if current password is correct
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'valid' => false,
                    'msg' => 'Current password is incorrect.'
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            return response()->json([
                'valid' => true,
                'msg' => 'Password successfully updated.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'valid' => false,
                'msg' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'msg' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }
}
