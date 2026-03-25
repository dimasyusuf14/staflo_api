<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index()
    {
        $users = User::with('position')->get();

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'data' => $users,
            'total' => $users->count(),
        ], 200);
    }

    /**
     * Get user by ID
     */
    public function show($id)
    {
        $user = User::with('position')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'data' => $user,
        ], 200);
    }

    /**
     * Get current authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = User::with('position')->find($request->user()->id);

        return response()->json([
            'message' => 'Data profil user berhasil diambil',
            'data' => $user,
        ], 200);
    }

    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        $users = User::with('position')->where('role', $role)->get();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => "User dengan role '$role' tidak ditemukan",
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => "Data user dengan role '$role' berhasil diambil",
            'data' => $users,
            'total' => $users->count(),
        ], 200);
    }

    /**
     * Update user data via POST
     * POST /api/users/{id}/update
     */
    public function update(Request $request, $id)
    {
        $authUser = $request->user();
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        // Check authorization
        if (!$this->canManageUser($authUser, $user)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengedit user ini',
            ], 403);
        }

        // Validate input
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|in:director,manager,staff',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update name if provided
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        // Update email if provided
        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Update role if provided
        if ($request->filled('role')) {
            $user->role = $request->input('role');
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                unlink(storage_path('app/public/' . $user->profile_photo));
            }

            // Store new photo
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $photoPath;
        }

        $user->save();

        return response()->json([
            'message' => 'Data user berhasil diperbarui',
            'data' => $user,
        ], 200);
    }

    /**
     * Delete user via POST
     * POST /api/users/{id}/delete
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $request->user();
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        // Check authorization
        if (!$this->canManageUser($authUser, $user)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus user ini',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus',
            'data' => $user,
        ], 200);
    }

    /**
     * Check if authenticated user can manage target user
     * Rules:
     * - Setiap user bisa edit akun sendiri
     * - Director (tingkat 1) bisa manage tingkat 2 (manager) dan 3 (staff)
     * - Manager (tingkat 2) bisa manage tingkat 3 (staff) saja
     * - Staff (tingkat 3) tidak bisa manage user lain
     */
    private function canManageUser($authUser, $targetUser)
    {
        // User bisa edit akun sendiri
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        // Director dapat manage siapa saja (manager dan staff)
        if ($authUser->role === 'director') {
            return true;
        }

        // Manager hanya bisa manage staff
        if ($authUser->role === 'manager' && $targetUser->role === 'staff') {
            return true;
        }

        // Staff dan permission lainnya tidak diizinkan
        return false;
    }
}
